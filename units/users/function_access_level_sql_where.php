<?php
	global $CFG;
	global $USER;

    // Returns an SQL "where" clause containing all the access codes that the user can see
    
        $run_result = " access = 'PUBLIC' ";
    
        if (logged_on) {
        	if (run("users:flags:get", array("admin", $_SESSION['userid']))) {
        		$run_result = " 0=0 ";
        	}
        	else {
	            $run_result = " owner = " . $_SESSION['userid'] . " ";
	            $run_result .= " OR access IN ('PUBLIC', 'LOGGED_IN', 'user" . $_SESSION['userid'] . "') ";
	            if ($CFG->owned_users_allaccess && $USER->owner == -1)
	            	$run_result .= " OR owner IN (SELECT ident FROM ".$CFG->prefix."users WHERE owner != -1 AND user_type = 'person') ";
        	}

        } else {
            
            $run_result = " access = 'PUBLIC' ";
            
         }
        
?>