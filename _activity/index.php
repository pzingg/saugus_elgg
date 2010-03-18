<?php
global $CFG;
global $USER;
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
//$starttime = optional_param('starttime',time()-86400,PARAM_INT);
$starttime = optional_param('starttime',$USER->last_action,PARAM_INT); //changed to show since last login instead

$body = "<p>" . gettext("Currently viewing recent activity since ") . (($starttime == $USER->last_action) ? "last login (". gmdate("F d, Y",$starttime).")" : gmdate("F d, Y",$starttime)) . ".</p>";

$body .= "<p>" . gettext("You may view recent activity during the following time-frames:") . "</p>";

$body .= "<ul><li><a href=\"index.php?starttime=" . $USER->last_action . "\">" . gettext("Since last login") . " (". gmdate("F d, Y",$starttime).")</a></li>";
$body .= "<li><a href=\"index.php?starttime=" . (time() - 86400) . "\">" . gettext("The last 24 hours") . "</a></li>";
$body .= "<li><a href=\"index.php?starttime=" . (time() - (86400 * 2)) . "\">" . gettext("The last 48 hours") . "</a></li>";
$body .= "<li><a href=\"index.php?starttime=" . (time() - (86400 * 7)) . "\">" . gettext("The last week") . "</a></li>";
$body .= "<li><a href=\"index.php?starttime=" . (time() - (86400 * 30)) . "\">" . gettext("The last month") . "</a></li></ul>";

$body .= "<h2>" . gettext("Activity on your weblog posts") . "</h2>";

// TODO: the foreach loops are identical and could possibly do with being functionised - Sven

// $where added here and below for moderated comments access controls
$where = "1=1"; //regular users can see all comments for themselves.
if ($USER->owner != -1) { //owned users can only see approved content
	$where = run("users:access_level_sql_where",$_SESSION['userid']);
	$where .= "or owner = " . $USER->owner . " "; //added so that students can see teacher private comments
	$where = preg_replace('/owner =/','wc.owner =',$where);
	$where = preg_replace('/access IN/','wc.access IN',$where);
}
if ($activities = get_records_sql('SELECT wc.*, u.username,u.name as weblogname, wp.ident AS weblogpost,wp.title AS weblogtitle, wp.weblog AS weblog
                                    FROM '.$CFG->prefix.'weblog_comments wc
                                    LEFT JOIN '.$CFG->prefix.'weblog_posts wp ON wp.ident = wc.post_id
                                    LEFT JOIN '.$CFG->prefix.'users u ON u.ident = wp.weblog 
                                    WHERE wc.posted >= ? AND wp.owner = ? AND ('.$where.')
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
                                    WHERE wc.posted > ? AND wl.owner = ? AND ('.$where.')
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