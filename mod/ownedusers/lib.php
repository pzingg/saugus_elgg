<?php

function ownedusers_pagesetup() {
    // register links -- 
    global $profile_id;
    global $PAGE;
    global $CFG;
    global $USER;
    
    $page_owner = $profile_id;
    
    if ($CFG->owned_users) {

		if (defined("context") && context == "network") {
	
			if (run("users:type:get", $page_owner) == "person") {
				
				$PAGE->menu_sub[] = array( 'name' => 'ownedusers:requests', 
	                                       'html' => '<a href="'.$CFG->wwwroot.'_ownedusers/owned.php?owner='.$page_owner.'">'
	                                       . gettext("Owned ".$CFG->owned_users_caption) . '</a>');
			}
							
		}
		if (defined("context") && context == "weblog") {
			if (run("users:type:get", $page_owner) == "person") {
				if ($result = get_records_select('users',"owner = ? AND user_type = ?",array($page_owner,'person'))) 
					$PAGE->menu_sub[] = array( 'name' => 'ownedusers:requests', 
	                                       'html' => '<a href="'.$CFG->wwwroot.$USER->username.'/weblog/ownedusers/'.'">'
	                                       . gettext($CFG->owned_users_caption."' blogs") . '</a>');
						
			}
		}
		
		if (defined("context") && context == "profile" && logged_on && !run("users:flags:get", array("admin", $USER->ident))) {
	       if ($result = get_records_select('users',"ident = ? and owner = ? AND user_type = ?",array($page_owner,$USER->ident,'person'))) {
	            $PAGE->menu_sub[] = array( 'name' => 'profile:edit', 
	                                       'html' => '<a href="'.$CFG->wwwroot.'profile/edit.php?profile_id='.$page_owner.'">'
	                                       . gettext("Edit this profile") . '</a>');
	
	            if (run("permissions:check", "profile")) {
	            
	                $PAGE->menu_sub[] = array( 'name' => 'owneduser:pic',
	                                           'html' => a_hrefg("{$CFG->wwwroot}_icons/?context=profile&amp;profile_id=$page_owner" ,
	                                                              gettext("Change site picture")));
	    
	                $PAGE->menu_sub[] = array( 'name' => 'profile:help',
	                                       'html' => '<a href="'.$CFG->wwwroot.'help/profile_help.php">'
	                                       . gettext("Page help") . '</a>');
	
	            }
	            
	        }
		}
    }
	
}

?>