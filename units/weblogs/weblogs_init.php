<?php
global $USER;
// ELGG weblog system initialisation

// ID of profile to view / edit

global $profile_id;

$weblog_name = optional_param('weblog_name');
if (!empty($weblog_name)) {
    $profile_id = (int) run("users:name_to_id", $weblog_name);
} else {
    if (isloggedin()) {
        $profile_id = optional_param('profile_id',optional_param('profileid',$USER->ident,PARAM_INT),PARAM_INT);
    } else {
        $profile_id = optional_param('profile_id',optional_param('profileid',-1,PARAM_INT),PARAM_INT);
    }
}

global $page_owner;

$page_owner = $profile_id;

global $page_userid;

$page_userid = run("users:id_to_name", $profile_id);

// Add RSS to metatags

global $metatags;
if (!empty($weblog_name))
	$metatags .= "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS\" href=\"".url."$page_userid/weblog/rss\" />\n";
else
	$metatags .= "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS\" href=\"".url."_weblog/rss2.php?modifier=all\" />\n";

?>