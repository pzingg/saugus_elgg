<?php

function file_pagesetup() {
    // register links -- 
    global $profile_id;
    global $PAGE;
    global $CFG;

    $page_owner = $profile_id;

    if (isloggedin()) {
        if (defined("context") && context == "files" && $page_owner == $_SESSION['userid']) {
            $PAGE->menu[] = array( 'name' => 'file',
                                   'html' => "<li><a href=\"{$CFG->wwwroot}{$_SESSION['username']}/files/\" class=\"selected\" >" .gettext("Your Files").'</a></li>');
        } else {
            $PAGE->menu[] = array( 'name' => 'files',
                                   'html' => "<li><a href=\"{$CFG->wwwroot}{$_SESSION['username']}/files/\" >" .gettext("Your Files").'</a></li>');
        }
    }

    if (defined("context") && context == "files") {
        
        $files_username = run("users:id_to_name",$page_owner);
        
        if ($page_owner == $_SESSION['userid'] && $page_owner != -1) {
            $PAGE->menu_sub[] = array( 'name' => 'file:add',
                                       'html' => a_href( "#addFile",
                                                          gettext("Add a file or a folder")));           
        }
        if ($page_owner != -1) {
            if ($page_owner == $_SESSION['userid'] && $page_owner != -1) {
                $PAGE->menu_sub[] = array( 'name' => 'file:rss',
                                           'html' => a_href( $CFG->wwwroot.$_SESSION['username']."/files/rss/", 
                                                              gettext("RSS feed for files")));  
            }
            if ($page_owner == $_SESSION['userid'] && $page_owner != -1) {
                $PAGE->menu_sub[] = array( 'name' => 'file:help',
                                           'html' => a_href( $CFG->wwwroot."help/files_help.php",
                                                              gettext("Page help")));  
            }
        }
    }
}

?>
