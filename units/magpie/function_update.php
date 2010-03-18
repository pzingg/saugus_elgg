<?php
global $CFG;
// $parameter = the ID number of the feed

// Convert $parameter to an integer, see if it exists
$parameter = (int) $parameter;
        
// Check database, get feed items
$feed = get_record('feeds','ident',$parameter);
$subscribers = count_records('feed_subscriptions','feed_id',$parameter);

if (!empty($feed) && !empty($subscribers)) {

    $update_time = 1;

    /* if ($subscribers > 10) {
        $update_time = 3600;
    } else if ($subscribers > 5) {
        $update_time = 4800;
    } else if ($subscribers > 1) {
        $update_time = 7200;
    } else {
        $update_time = 14400;
    } */
    
    // TODO: adjust system so the presence of tons of feeds with few subscribers
    // doesn't clog up the cron
    $update_time = 3600;

    $timenow = time();
    
    if ($feed->last_updated < ($timenow - $update_time)) {
        set_field('feeds','last_updated',$timenow,'ident',$parameter);
        if ($rss = run("rss:get", $feed->url)) {
            
            $feedtitle = trim(stripslashes($rss->channel['title']));
            $feedtagline = trim(stripslashes($rss->channel['tagline']));
            if (strlen($feedtagline) > 120) {
                $feedtagline = "";
            }
            $feedurl = trim(stripslashes($rss->channel['link']));
            $f = new StdClass;
            $f->siteurl = $feedurl;
            $f->name = $feedtitle;
            $f->tagline = $feedtagline;
            $f->ident = $parameter;
            update_record('feeds',$f);
            $feeditems = array();
            if ($feeditemstemp = get_records('feed_posts','feed',$parameter)) {
                foreach($feeditemstemp as $feeditem) {
                    $feeditems[$feeditem->ident] = stripslashes($feeditem->url);
                }
            }
            unset($feeditemstemp);
            
            if (sizeof($rss->items > 0)) {
                
                $mintime = $timenow - ($CFG->rsspostsmaxage * 86400);
                
                foreach($rss->items as $item) {
                    $title = trim(stripslashes($item['title']));
                    $description = trim(stripslashes($item['description']));
                    if (isset($item['atom_content'])) {
                        $description = trim(stripslashes($item['atom_content']));
                    }
                    if (isset($item['dc']['date'])) {
                        $posted = stripslashes($item['dc']['date']);
                    } elseif (isset($item['issued'])) {
                        $posted = stripslashes($item['issued']);
                    } else {
                        $posted = stripslashes($item['pubdate']);
                    }
                    $posted = str_replace("T"," ",$posted);
                    $posted = str_replace("Z"," ",$posted);
                    $posted = str_replace("GM"," ",$posted);
                    $posted = str_replace("ES"," ",$posted);
                    $posted = str_replace("PS"," ",$posted);
                    $posted = str_replace("ue","Tue",$posted);
                    $posted = str_replace("hu","Thu",$posted);
                    $posted = preg_replace('/(\d\d\d\d)\-(\d\d)\-(\d\d)/','$1/$2/$3',$posted);
                    $posted = trim(preg_replace('/(\-.*)/','',$posted));
                    
                    $url = trim(stripslashes($item['link']));
                    $url = substr($url, 0, 255); // trim urls down to the max length in the db, just in case. CURSE YOU, GUARDIAN BLOGGERS!
                    
                    if (!empty($item['date_timestamp'])) {
                        $added = (int) $item['date_timestamp'];
                    }
                    if (!$added && ($posted == "" || !($added = @strtotime($posted))) ) {
                        $added = $timenow;
                    }
                    if ($added > $timenow || $added == -1) {
                        $added = $timenow;
                    }
                    
                    if (!$CFG->rsspostsmaxage || $added > $mintime) {
                        //don't update/insert feed posts older than the pruning age
                        
                        if (in_array($url,$feeditems)) {
                            // update_record is not going to work here, we don't have a primary key that I can see (Penny)
                            $fp = new StdClass;
                            $fp->ident = array_search($url,$feeditems);
                            $fp->title = $title;
                            $fp->body =  $description;
                            $fp->posted = $posted;
                            $fp->url = $url;
                            $fp->feed = $parameter;
                            update_record('feed_posts',$fp);
                        } else {
                            $fp = new StdClass;
                            $fp->title = $title;
                            $fp->body = $description;
                            $fp->posted = $posted;
                            $fp->url = $url;
                            $fp->feed = $parameter;
                            $fp->added = $added;
                            insert_record('feed_posts',$fp);
                            if ($weblogs = get_records_select('feed_subscriptions','feed_id = ? AND autopost = ?',array($parameter,'yes'))) {
                                $body = "<p><a href=\"$url\">$url</a></p> " . $description;
                                foreach($weblogs as $weblog) {
                                    $wp = new StdClass;
                                    $wp->title = $title;
                                    $wp->body = $body;
                                    $wp->access = 'PUBLIC';
                                    $wp->owner = $weblog->user_id;
                                    $wp->weblog = $weblog->user_id;
                                    $wp->posted = $added;
                                    $id = insert_record('weblog_posts',$wp);
                                    $tags = trim($weblog->autopost_tag);
                                    insert_tags_from_string ($tags, 'weblog', $id, 'PUBLIC', $weblog_user_id);
                                    $rssresult = run("weblogs:rss:publish", array($weblog->user_id, false));
                                    $rssresult = run("profile:rss:publish", array($weblog->user_id, false));
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

?>