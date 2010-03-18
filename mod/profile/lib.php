<?php

function profile_pagesetup() {
    // register links -- 
    global $profile_id;
    global $PAGE;
    global $CFG;

    // don't clobber $page_owner, use a 
    // local $pgowner instead for clarity
    $pgowner = $profile_id;

    if (isloggedin()) {
        if (defined("context") && context == "profile" && $pgowner == $_SESSION['userid']) {
            $PAGE->menu[] = array( 'name' => 'profile:me', 
                                   'html' => '<li><a href="'.$CFG->wwwroot.$_SESSION['username'].'" class="selected">'.gettext("Your Profile").'</a></li>');
        } else {
            $PAGE->menu[] = array( 'name' => 'profile:me',
                                   'html' => '<li><a href="'.$CFG->wwwroot.$_SESSION['username'].'">'.gettext("Your Profile").'</a></li>');
        }

        if (profile_permissions_check("profile") && defined("context") && context == "profile") {

            if (run("users:type:get", $pgowner) == "person") {
                $PAGE->menu_sub[] = array( 'name' => 'profile:edit', 
                                           'html' => '<a href="'.$CFG->wwwroot.'profile/edit.php?profile_id='.$pgowner.'">'
                                           . gettext("Edit this profile") . '</a>');

                $PAGE->menu_sub[] = array( 'name' => 'profile:picedit', 
                                           'html' => '<a href="'.$CFG->wwwroot.'_icons/?context=profile&amp;profile_id='.$pgowner.'">'
                                           . gettext("Change site picture") . '</a>');
            }
            $PAGE->menu_sub[] = array( 'name' => 'profile:help',
                                       'html' => '<a href="'.$CFG->wwwroot.'help/profile_help.php">'
                                       . gettext("Page help") . '</a>');
        }
    }
}

function profile_permissions_check ($object) {
    global $page_owner;
    
    if ($object === "profile" && ($page_owner == $_SESSION['userid'] || run("users:flags:get", array("admin", $_SESSION['userid'])))) {
        return true;
    }
    return false;
}


?>