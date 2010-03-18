<?php

function blog_pagesetup() {
    // register links -- 
    global $profile_id;
    global $PAGE;
    global $CFG;

    $page_owner = $profile_id;

    // main menu
    if (isloggedin()) {

        if (defined("context") && context == "weblog" && $page_owner == $_SESSION['userid']) {
            
            $PAGE->menu[] = array( 'name' => 'blog',
                                   'html' => "<li><a href=\"{$CFG->wwwroot}{$_SESSION['username']}/weblog\" class=\"selected\" >" .gettext("Your Blog").'</a></li>');
            
        } else {
            $PAGE->menu[] = array( 'name' => 'blog',
                                   'html' => "<li><a href=\"{$CFG->wwwroot}{$_SESSION['username']}/weblog\" >" .gettext("Your Blog").'</a></li>');
        };
    }

    $weblog_username = run("users:id_to_name",$page_owner);
    
    // submenu
    if (defined("context") && context == "weblog") {
        
        if ($page_owner != -1) {

            $PAGE->menu_sub[] = array ( 'name' => 'blog:rssfeed',
                                        'html' => "<a href=\"{$CFG->wwwroot}{$weblog_username}/weblog/rss/\"><img src=\"{$CFG->wwwroot}_templates/icons/rss.png\" border=\"0\" alt=\"rss\" /></a>"); 

            
            if (run("permissions:check", "weblog") && logged_on) {
                               $PAGE->menu_sub[] = array ( 'name' => 'blog:post',
                                           'html' => "<a href=\"{$CFG->wwwroot}_weblog/edit.php?owner=$page_owner\">"
                                           . gettext("Post a new entry") . '</a>');
            }

            $PAGE->menu_sub[] = array ( 'name' => 'blog:view',
                                        'html' => "<a href=\"{$CFG->wwwroot}{$weblog_username}/weblog/\">"
                                        . gettext("View blog") . '</a>');

            $PAGE->menu_sub[] = array ( 'name' => 'blog:archive',
                                        'html' => "<a href=\"{$CFG->wwwroot}{$weblog_username}/weblog/archive/\">"
                                        . gettext("Archive") . '</a>'); 

            $PAGE->menu_sub[] = array ( 'name' => 'blog:friends',
                                        'html' => "<a href=\"{$CFG->wwwroot}{$weblog_username}/weblog/friends/\">"
                                        . gettext("Friends' blogs") . '</a>'); 
            
        }
        
        $PAGE->menu_sub[] = array ( 'name' => 'blog:everyone',
                                    'html' => "<a href=\"{$CFG->wwwroot}_weblog/everyone.php\">"
                                    . gettext("View all posts") . '</a>'); 
        
        $PAGE->menu_sub[] = array ( 'name' => 'blog:help',
                                    'html' => "<a href=\"{$CFG->wwwroot}help/blogs_help.php\">"
                                    . gettext("Page help") . '</a>'); 

    }

}
    
?>