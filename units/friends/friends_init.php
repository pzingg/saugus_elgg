<?php

global $owner;
global $page_owner;
global $profile_id;

$friends_name = optional_param('friends_name');
if (!empty($friends_name)) {
    $owner = run("users:name_to_id",$friends_name);
} else {
    $owner = optional_param('owner',$page_owner,PARAM_INT);
}
if (empty($owner)) {
    $owner = -1;
}

/*if (logged_on) {
    $owner = (int) $_SESSION['userid'];
}*/

$page_owner = $owner;
$profile_id = $owner;

global $page_userid;

$page_userid = run("users:id_to_name", $page_owner);

global $metatags;

if ($owner != -1) {
    $metatags .= "<link rel=\"meta\" type=\"application/rdf+xml\" title=\"FOAF\" href=\"".url."$page_userid/foaf\" />";
}
        
?>