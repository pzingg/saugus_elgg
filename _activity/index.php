<?php
global $CFG;
//    ELGG recent activity page

// Run includes
require_once(dirname(dirname(__FILE__))."/includes.php");

// Initialise functions for user details, icon management and profile management
run("profile:init");

// Whose friends are we looking at?
global $page_owner;

// Weblog context
define("context", "weblog");

// You must be logged on to view this!
protect(1);

templates_page_setup();

$title = run("profile:display:name") . " :: " . gettext("Recent activity");

// If we haven't specified a start time, start time = 1 day ago
$starttime = optional_param('starttime',time()-86400,PARAM_INT);

$body = "<p>" . gettext("Currently viewing recent activity since ") . gmdate("F d, Y",$starttime) . ".</p>";

$body .= "<p>" . gettext("You may view recent activity during the following time-frames:") . "</p>";

$body .= "<ul><li><a href=\"index.php?starttime=" . (time() - 86400) . "\">" . gettext("The last 24 hours") . "</a></li>";
$body .= "<li><a href=\"index.php?starttime=" . (time() - (86400 * 2)) . "\">" . gettext("The last 48 hours") . "</a></li>";
$body .= "<li><a href=\"index.php?starttime=" . (time() - (86400 * 7)) . "\">" . gettext("The last week") . "</a></li>";
$body .= "<li><a href=\"index.php?starttime=" . (time() - (86400 * 30)) . "\">" . gettext("The last month") . "</a></li></ul>";

$body .= "<h2>" . gettext("Activity on your weblog posts") . "</h2>";

// TODO: the foreach loops are identical and could possibly do with being functionised - Sven

if ($activities = get_records_sql('SELECT wc.*, u.username,u.name as weblogname, wp.ident AS weblogpost,wp.title AS weblogtitle, wp.weblog AS weblog
                                    FROM '.$CFG->prefix.'weblog_comments wc
                                    LEFT JOIN '.$CFG->prefix.'weblog_posts wp ON wp.ident = wc.post_id
                                    LEFT JOIN '.$CFG->prefix.'users u ON u.ident = wp.weblog 
                                    WHERE wc.posted >= ? AND wp.owner = ? 
                                    ORDER BY wc.posted DESC',array($starttime,$page_owner))) {
    
    foreach($activities as $activity) {
        $commentbody = stripslashes($activity->body);
        $commentbody .= "<br /><br /><a href=\"" . url . $activity->username . "/weblog/" . $activity->weblogpost . ".html\">" . gettext("Read more") . "</a>";
        $activity->postedname = stripslashes($activity->postedname);
        $activity->weblogname = stripslashes($activity->weblogname);
        if ($activity->weblog == $USER->ident) {
            $activity->weblogname = gettext("your blog");
        }
        if ($activity->owner == $USER->ident) {
            $commentposter = sprintf(gettext("<b>You</b> commented on weblog post '%s' in %s:"), stripslashes($activity->weblogtitle), $activity->weblogname);
        } else {
            $commentposter = sprintf(gettext("<b>%s</b> commented on weblog post '%s' in %s:"), $activity->postedname, stripslashes($activity->weblogtitle), $activity->weblogname);
        }
        $body .= templates_draw(array(
                                        'context' => 'databox1',
                                        'name' => $commentposter,
                                        'column1' => $commentbody
                                      )
                                );
    }
} else {
    $body .= "<p>" . gettext("No activity during this time period.") . "</p>";
}

$body .= "<h2>" . gettext("Activity on weblog posts you have marked as interesting") . "</h2>";

if ($activities = get_records_sql('SELECT wc.*, u.username, u.name as weblogname, wp.weblog, wp.ident AS weblogpost, wp.title AS weblogtitle, wp.weblog AS weblog 
                                    FROM '.$CFG->prefix.'weblog_comments wc 
                                    LEFT JOIN '.$CFG->prefix.'weblog_watchlist wl ON wl.weblog_post = wc.post_id 
                                    LEFT JOIN '.$CFG->prefix.'weblog_posts wp ON wp.ident = wc.post_id 
                                    LEFT JOIN '.$CFG->prefix.'users u ON u.ident = wp.weblog 
                                    WHERE wc.posted > ? AND wl.owner = ?
                                    ORDER BY wc.posted DESC',array($starttime, $page_owner))) {
    foreach($activities as $activity) {
        $commentbody = stripslashes($activity->body);
        $commentbody .= "<br /><br /><a href=\"" . url . $activity->username . "/weblog/" . $activity->weblogpost . ".html\">" . gettext("Read more") . "</a>";
        $activity->postedname = stripslashes($activity->postedname);
        $activity->weblogname = stripslashes($activity->weblogname);
        if ($activity->weblog == $USER->ident) {
            $activity->weblogname = gettext("your blog");
        }
        if ($activity->owner == $USER->ident) {
            $commentposter = sprintf(gettext("<b>You</b> commented on weblog post '%s' in %s:"), stripslashes($activity->weblogtitle), $activity->weblogname);
        } else {
            $commentposter = sprintf(gettext("<b>%s</b> commented on weblog post '%s' in %s:"), $activity->postedname, stripslashes($activity->weblogtitle), $activity->weblogname);
        }
        $body .= templates_draw(array(
                                        'context' => 'databox1',
                                        'name' => $commentposter,
                                        'column1' => $commentbody
                                      )
                                );
    }
} else {
    $body .= "<p>" . gettext("No activity during this time period.") . "</p>";
}

$body = templates_draw(array(
                             'context' => 'contentholder',
                             'title' => $title,
                             'body' => $body
                             )
                       );

echo templates_page_draw( array(
                                  $title, $body
                                  )
         );

?>