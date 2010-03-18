<?php
global $page_owner;
global $USER;
global $CFG;
		
		if ($parameter[0] == "userdetails:change") {
			if ($result = get_records_select('users',"ident = ? AND user_type = ? AND owner = ?",array($page_owner,'person',$USER->ident))) 
				$run_result = true;
		}
		if (logged_on) {
			
			// $parameter[0] = context
			// $parameter1[1] = $post->owner
			
			if ($parameter[0] == "weblog:edit") {
				$poster = get_record('users','ident',$parameter[1]);
				if (($poster->owner == $USER->ident) || (($poster->owner != -1) && ($USER->owner == -1) && $CFG->owned_users_allaccess))
					$run_result = true;
			}
			if ($parameter == "profile") {
				$poster = get_record('users','ident',$page_owner);
				if ($poster->owner == $USER->ident)
					$run_result = true;
			}
			if (($parameter == "weblog") || ($parameter == "weblog:comment")) {
				$poster = get_record('users','ident',$page_owner);
				if (($poster->owner == $USER->ident) || (($poster->owner != -1) && ($USER->owner == -1) && $CFG->owned_users_allaccess))
					$run_result = true;
			}
			if ($parameter[0] == "files:edit") {
				$poster = get_record('users','ident',$parameter[1]);
				if (($poster->owner == $USER->ident) || (($poster->owner != -1) && ($USER->owner == -1) && $CFG->owned_users_allaccess))
					$run_result = true;
			}
			if ($parameter == "uploadicons") {
	            
	            if (get_field("users","owner","ident",$page_owner) == $_SESSION['userid']) {
	                $run_result = true;
	            }
	            
	        }
			
		}
		
?>
