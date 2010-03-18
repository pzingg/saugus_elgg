<?php

function newsclient_pagesetup() {
    // register links -- 
    global $profile_id;
    global $PAGE;
    global $CFG;

    $page_owner = $profile_id;

    if (isloggedin()) {
        if (defined("context") && context == "resources" && $page_owner == $_SESSION['userid']) {
            $PAGE->menu[] = array( 'name' => 'resources',
                                   'html' => "<li><a href=\"{$CFG->wwwroot}{$_SESSION['username']}/feeds/\" class=\"selected\" >" .gettext("Your Resources").'</a></li>');
        } else {
            $PAGE->menu[] = array( 'name' => 'resources',
                                   'html' => "<li><a href=\"{$CFG->wwwroot}{$_SESSION['username']}/feeds/\" >" .gettext("Your Resources").'</a></li>');
        }

        $rss_username = run("users:id_to_name",$page_owner);
    }

    if (defined("context") && context == "resources") {
    
        if ($page_owner != -1) {
            if (run("permissions:check", "rss") && logged_on && $page_owner == $_SESSION['userid']) {
                $PAGE->menu_sub[] = array( 'name' => 'newsfeed:subscription',
                                           'html' => a_href( $CFG->wwwroot.$_SESSION['username']."/feeds/", 
                                                              gettext("Feeds")));
                $PAGE->menu_sub[] = array( 'name' => 'newsfeed:subscription:publish:blog',
                                           'html' => a_href( $CFG->wwwroot."_rss/blog.php?page_owner=" . $_SESSION['userid'], 
                                                              gettext("Publish to blog")));
            }
            $PAGE->menu_sub[] = array( 'name' => 'newsclient',
                                       'html' => a_href( $CFG->wwwroot.$rss_username."/feeds/all/", 
                                                          gettext("View aggregator")));
        }
        $PAGE->menu_sub[] = array( 'name' => 'feed',
                                   'html' => a_href( $CFG->wwwroot."_rss/popular.php",
                                                      gettext("Popular Feeds")));

        /*
        $PAGE->menu_sub[] = array( 'name' => 'feed',
                                   'html' => a_href( $CFG->wwwroot."help/feeds_help.php", 
                                                      "Page help"));
        */

    }
}

function newsclient_cron() {
    global $CFG;

    // if we've run in the last day, skip it
    if (!empty($CFG->newsclient_lastcron) && (time() - (60*60*24)) < $CFG->newsclient_lastcron) {
        return true;
    }
    
    run("weblogs:init");
    run("profile:init");
    run("rss:init");
    
    define('context','resources');
    
    run('rss:prune');
    
    //$users = get_records('users');
    //foreach ($users as $user) {
    //  run("rss:update:all:cron",$user->ident);
    //}
    run("rss:update:all:cron");

    set_config('newsclient_lastcron',time());
    
    
}

?>
