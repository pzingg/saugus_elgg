<?php

function ldap_pagesetup() {
    // register links -- 
    global $profile_id;
    global $PAGE;
    global $CFG;
    global $USER;
    
    $page_owner = $profile_id;
    
    if (($CFG->auth == 'ldap')) {

		if (isloggedin() && defined("context") && context == "admin" && run("users:flags:get", array("admin", $_SESSION['userid']))) {
		
				$PAGE->menu_sub[] = array( 'name' => 'admin:ldap',
                                  'html' => a_hrefg("{$CFG->wwwroot}_admin/ldap.php",
                                                   "LDAP"));
							
		}
		
    }
	
}

?>