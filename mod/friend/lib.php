<?php

function friend_pagesetup() {
    // register links -- 
    global $profile_id;
    global $PAGE;
    global $CFG;

    $page_owner = $profile_id;

    if (isloggedin()) {
        if (defined("context") && context == "network" && $page_owner == $_SESSION['userid']) {
            
            $PAGE->menu[] = array( 'name' => 'network',
                                   'html' => "<li><a href=\"{$CFG->wwwroot}{$_SESSION['username']}/friends/\" class=\"selected\" >" .gettext("Your Network").'</a></li>');
            } else {
                $PAGE->menu[] = array( 'name' => 'network',
                                       'html' => "<li><a href=\"{$CFG->wwwroot}{$_SESSION['username']}/friends/\" >" .gettext("Your Network").'</a></li>');
            }
    }        

    if (defined("context") && context == "network") {
        
        if (run("users:type:get", $page_owner) == "person") {
        
            $friends_username = run("users:id_to_name",$page_owner);
            
            $PAGE->menu_sub[] = array( 'name' => 'friend',
                                       'html' => a_hrefg("{$CFG->wwwroot}{$friends_username}/friends/" ,
                                                          gettext("Friends"))); 
            
            $PAGE->menu_sub[] = array( 'name' => 'friend:of',
                                       'html' => a_hrefg( "{$CFG->wwwroot}_friends/friendsof.php?owner=$page_owner",
                                                          gettext("Friend of"))); 

            $PAGE->menu_sub[] = array( 'name' => 'friend:requests',
                                       'html' => a_hrefg( "{$CFG->wwwroot}_friends/requests.php?owner=$page_owner",
                                                          gettext("Friendship requests")));
            
            if ($CFG->foaf_enabled)
            	$PAGE->menu_sub[] = array( 'name' => 'friend:foaf',
                                       'html' => a_hrefg( "{$CFG->wwwroot}{$friends_username}/foaf/",
                                                          gettext("FOAF"))); 

            if (isloggedin()) {
                $PAGE->menu_sub[] = array( 'name' => 'friend:accesscontrols',
                                           'html' => a_hrefg( "{$CFG->wwwroot}_groups/",
                                                              gettext("Access controls")));

                if ($CFG->publicinvite == true) {
                    $PAGE->menu_sub[] = array( 'name' => 'friend:invite',
                                               'html' => a_hrefg( "{$CFG->wwwroot}_invite/",
                                                                  gettext("Invite a friend"))); 
                }
                
                $PAGE->menu_sub[] = array( 'name' => 'friend:help',
                                           'html' => a_hrefg( "{$CFG->wwwroot}help/network_help.php",
                                                              gettext("Page help")));
                
                

            }
            
        }
    }

}

?>
