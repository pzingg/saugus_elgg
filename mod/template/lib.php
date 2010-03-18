<?php

function template_pagesetup() {
    // register links -- 
    global $profile_id;
    global $PAGE;
    global $CFG;

    $page_owner = $profile_id;

    if (defined("context") && context == "account" && !$CFG->disable_templatechanging) {
        if ($page_owner == $_SESSION['userid'] && $page_owner != -1) {
            $PAGE->menu_sub[] = array( 'name' => 'template:change',
                                       'html' => a_href( "{$CFG->wwwroot}_templates/",
                                                          gettext("Change theme")));  
        }
    }
}

?>
