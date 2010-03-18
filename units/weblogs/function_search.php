<?php
global $CFG, $USER, $db;
global $search_exclusions;
    
$weblogPosted = gettext("Weblog posts by"); // gettext variable
$inCategory = gettext("in category"); // gettext variable
$rssForBlog = gettext("RSS feed for weblog posts by"); // gettext variable
$otherUsers = gettext("Other users with weblog posts in category"); // gettext variable
$otherUsers2 = gettext("Users with weblog posts in category"); // gettext variable
$viewAll = gettext("View all weblog posts in category"); // gettext variable

if (isset($parameter) && $parameter[0] == "weblog" || $parameter[0] == "weblogall") {
    
    if ($parameter[0] == "weblog") {
        $search_exclusions[] = "weblogall";
        $owner = optional_param('owner',0,PARAM_INT);
        $searchline = "tagtype = 'weblog' AND owner = $owner AND tag = " . $db->qstr($parameter[1]) . "";
        $searchline = "(" . run("users:access_level_sql_where",$_SESSION['userid']) . ") AND " . $searchline;
        $searchline = str_replace("owner","t.owner",$searchline);
        if ($refs = get_records_select('tags t',$searchline)) {
            $searchline = "";
            foreach($refs as $ref) {
                if ($searchline != "") {
                    $searchline .= ",";
                }
                $searchline .= $ref->ref;
            }
            $searchline = " wp.ident in (" . $searchline . ")";
            if (!$posts = get_records_sql('SELECT wp.ident,u.name,u.username,u.ident as userid, wp.title, wp.ident, wp.weblog, wp.owner, wp.posted
                                     FROM '.$CFG->prefix.'weblog_posts wp JOIN '.$CFG->prefix.'users u ON u.ident = wp.owner
                                     WHERE ('.$searchline.') ORDER BY posted DESC')) {
                $posts = array(); // avoid warnings
            }
             // TODO I don't like this, but I can't understand why it's there, so I'm leaving it.
            $name = '';
            $username = '';
            if (count($posts) >= 1) {
                $keys = array_keys($posts);
                $p = $posts[$keys[0]];
                if (!empty($p)) {
                    // $name = stripslashes($p->name);
                    $name = run("profile:display:name",$p->userid);
                    $username = $p->username;
                }
            }
            $run_result .= "<h2>$weblogPosted " . $name . " $inCategory '".$parameter[1]."'</h2>\n<ul>";
            foreach($posts as $post) {
                $run_result .= "<li>";
                $weblogusername = run("users:id_to_name",$post->weblog);
                $run_result .= "<a href=\"".url . $weblogusername . "/weblog/" . $post->ident . ".html\">" . gmstrftime("%B %d, %Y",$post->posted) . " - " . stripslashes($post->title) . "</a>\n";
                if ($post->owner != $post->weblog) {
                    $run_result .= " @ " . "<a href=\"" . url . $weblogusername . "/weblog/\">" . $weblogusername . "</a>\n";
                }
                $run_result .= "</li>";
            }
            $run_result .= "</ul>";
            $run_result .= "<p><small>[ <a href=\"".url.$username . "/weblog/rss/" . $parameter[1] . "\">$rssForBlog " . $name . " $inCategory '".$parameter[1]."'</a> ]</small></p>\n";
        }
                    } else {
                        $icon = "default.png";
    }
    $searchline = "tagtype = 'weblog' and tag = " . $db->qstr($parameter[1]) . "";
    $searchline = "(" . run("users:access_level_sql_where",$_SESSION['userid']) . ") AND " . $searchline;
    $searchline = str_replace("owner","t.owner",$searchline);
    $sql = "SELECT DISTINCT u.* FROM ".$CFG->prefix.'tags t JOIN '.$CFG->prefix.'users u ON u.ident = t.owner WHERE ('.$searchline.')';
    if ($parameter[0] == "weblog") {
        $sql .= " and u.ident != " . $owner;
    }
    if ($users = get_records_sql($sql)) {
        if ($parameter[0] == "weblog") {
            $run_result .= "<h2>$otherUsers '".$parameter[1]."'</h2>\n";
        } else {
            $run_result .= "<h2>$otherUsers2 '".$parameter[1]."'</h2>\n";
        }
        $body = "<table><tr>";
        $i = 1;
        $w = 100;
        if (sizeof($users) > 4) {
            $w = 50;
        }
        foreach($users as $key => $info) {
            $friends_userid = $info->ident;
            // $friends_name = htmlspecialchars(stripslashes($info->name), ENT_COMPAT, 'utf-8');
            $friends_name = run("profile:display:name",$info->ident);
            $info->icon = run("icons:get",$info->ident);
            $friends_menu = run("users:infobox:menu",array($info->ident));
            $link_keyword = urlencode($parameter[1]);
            $body .= <<< END
        <td align="center">
            <p>
            <a href="{$CFG->wwwroot}search/index.php?weblog={$link_keyword}&amp;owner={$friends_userid}">
            <img src="{$CFG->wwwroot}{$info->username}/icons/{$info->icon}/w/{$w}" alt="{$friends_name}" border="0" /></a><br />
            <span class="userdetails">
                {$friends_name}
                {$friends_menu}
            </span>
            </p>
        </td>
END;
                if ($i % 5 == 0) {
                    $body .= "\n</tr><tr>\n";
                }
                $i++;
        }
        $body .= "</tr></table>";
        $body .= "<a href=\"".url."_weblog/everyone.php?filter=tag&filtervalue=".urlencode(stripslashes($parameter[1]))."\">".$viewAll." '".$parameter[1]."'</a>";
        $run_result .= $body;
    }
    
}

?>