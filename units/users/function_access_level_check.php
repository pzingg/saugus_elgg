<?php

global $USER,$page_owner,$CFG;

// Given the access level of a particular object, returns TRUE if the current user
// can access it, and FALSE if they can't

if (isset($parameter) && $parameter != "") {
    if ($parameter == "PUBLIC") {
        $run_result = true;
    } else if ($parameter == "LOGGED_IN" && isset($_SESSION['userid']) && $_SESSION['userid'] != "" && $_SESSION['userid'] != -1) {
        $run_result = true;
    } else if (substr_count($parameter, "user") > 0 && isset($_SESSION['userid'])) {
        $usernum = substr($parameter, 4, 15);
        if (($usernum == $_SESSION['userid']) || (run("users:flags:get", array("admin", $_SESSION['userid'])))) {
        //if ($usernum == $_SESSION['userid']) {
            $run_result = true;
        }
        if ((!$run_result) && logged_on && $CFG->owned_users_allaccess && ($USER->owner == -1)) {
        	if (get_field("users","owner","ident",$page_owner) != -1)
        		$run_result = true;
        }
    }
} else {
    $run_result = false;
}

?>