<?php
global $USER, $CFG;
global $page_owner;

$action = optional_param('action');
if (logged_on && run("permissions:check", "profile")) {
    if ($page_owner != $_SESSION['userid']) {
        $page_username = get_field('users','username','ident',$page_owner);
    } else {
        $page_username = $_SESSION['username'];
    }
    
    switch ($action) {
        
       
    // Subscribe to an existing feed
        case "subscribe":
            $feed = new StdClass;
            $feed->feed_id = optional_param('feed');
            if (!empty($feed->feed_id) && !run("rss:subscribed",$feed->feed_id)) {
                $feed->user_id = $page_owner;
                insert_record('feed_subscriptions',$feed);
                $messages[] = gettext("Your feed subscription was successful.");
            } else {
                $messages[] = gettext("Feed subscription failed: you are already subscribed to this feed.");
            }
            break;
        
        // Unsubscribe from an existing feed
        case "unsubscribe":                        
            $feed = optional_param('feed');
            if (!empty($feed) && run("rss:subscribed",$feed)) {
                delete_records('feed_subscriptions','feed_id',$feed,'user_id',$page_owner);
                $messages[] = gettext("Your have successfully removed this feed from your subscriptions.");
            } else {
                $messages[] = gettext("Feed unsubscription failed: you are not subscribed to this feed.");
            }
            break;
                
        case "rss:subscriptions:update":
            $keywords = optional_param('keywords');
            if (!empty($keywords)) {
                set_field('feed_subscriptions','autopost','no','user_id',$page_owner);
                if (is_array($keywords) && count($keywords) > 0) {
                    foreach($keywords as $key => $keyword_set) {
                        $keyword_set = trim($keyword_set);
                        if (strlen($keyword_set) > 128) {
                            $keyword_set = substr($keyword_set, 0, 128);
                        }
                        $key = (int) $key;
                        set_field('feed_subscriptions','autopost_tag',$keyword_set,'ident',$key,'user_id',$page_owner);
                    }
                }
                $feedautopost = optional_param('feedautopost');
                if (is_array($feedautopost) && count($feedautopost) > 0) {
                    foreach($feedautopost as $autopost) {
                        $autopost = (int)$autopost;
                        if ($feedurl = get_field_sql('SELECT f.url FROM '.$CFG->prefix.'feed_subscriptions fs 
                                                       LEFT JOIN '.$CFG->prefix.'feeds f ON f.ident = fs.feed_id 
                                                       WHERE fs.ident = '.$autopost)) {
                            if (substr_count($feedurl,url) > 0 && substr_count($feedurl,"/".$page_username."/") > 0) {
                                $messages[] = gettext("Feed not imported to blog: You cannot import a feed from this account.");
                                echo $feedurl;
                            } else {
                                set_field('feed_subscriptions','autopost','yes','ident',$autopost,'user_id',$page_owner);
                            }
                        }
                    }
                }
                $messages[] = "Your changes were saved.";
            }
            
        case "subscribe-new":
            $url = trim(optional_param('url'));
            if (!empty($url)) {
                if (substr($url,0,7) != "http://") {
                    $url = "http://" . $url;
                }
                $url = preg_replace('/#.*$/', '', $url);
                // $url = str_replace("@","",$url);
                if ($feed_exists = get_field('feeds','ident','url',$url)) {
                    if (!run("rss:subscribed",$feed_exists)) {
                        $feed = new StdClass;
                        $feed->feed_id = $feed_exists;
                        $feed->user_id = $page_owner;
                        insert_record('feed_subscriptions',$feed);
                        $messages[] = gettext("Your feed subscription was successful.");
                    } else {
                        $messages[] = gettext("Feed subscription failed: this feed subscription already exists.");
                    }
                } else if ($rss = @file_get_contents($url)) {
                    if (substr_count($rss,"<channel") > 0 || substr_count($rss,"<feed") > 0) {
                        $feed = new StdClass;
                        $feed->url = $url;
                        $feed->name = '';
                        $ident = insert_record('feeds',$feed);
                        $fs = new StdClass;
                        $fs->feed_id = $ident;
                        $fs->user_id = $page_owner;
                        insert_record('feed_subscriptions',$fs);
                        $messages[] = gettext("Your feed subscription was successful.");
                    } else {
                        $messages[] = gettext("Feed subscription failed: feed appears to be invalid. Please check your link or try later.");
                    }
                    
                } else {
                    $messages[] = gettext("Feed subscription failed: could not get feed. Please check your link or try later.");
                }
            }
            break;
    }
}

?>