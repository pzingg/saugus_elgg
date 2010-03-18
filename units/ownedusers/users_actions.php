<?php
	global $USER;
	global $page_owner;
	global $CFG;

	// Actions to perform on the owned users screen
	
		if (isset($_REQUEST['action'])) {
			switch($_REQUEST['action']) {
				
				// Create a new owned user
				case "owneduser:create":
										if (logged_on
											&& isset($_REQUEST['user_name'])
											&& isset($_REQUEST['user_username'])
											&& isset($_REQUEST['user_password'])) {
												
												if (!preg_match("/^[A-Za-z0-9]{3,12}$/",$_REQUEST['user_username'])) {
													$messages[] = gettext("Error! The username must contain letters and numbers only, cannot be blank, and must be between 3 and 12 characters in length.");
												} else if (trim($_REQUEST['user_name']) == "") {
													$messages[] = gettext("Error! The user's real name cannot be blank.");
												} else if (!preg_match("/^[A-Za-z0-9]{3,12}$/",$_REQUEST['user_password'])) {
													$messages[] = gettext("Error! Invalid password; passwords must contain letters and numbers only, cannot be blank, and must be between 3 and 12 characters in length.");
												} else {
													$username = strtolower(addslashes(trim($_REQUEST['user_username'])));
													if (record_exists('users','username',$username)) {
														$messages[] = sprintf(gettext("The username $s is already taken by another user. You will need to pick a different one."), $username);
													} else {
														$name = addslashes($_REQUEST['user_name']);
														$md5password = md5($_REQUEST['user_password']);
														$u = new StdClass;
									                    $u->name = $name;
									                    $u->username = $username;
									                    $u->password = $md5password;
									                    $u->user_type = 'person';
									                    $u->owner = $USER->ident;
									                    $uid = insert_record('users',$u);
									                    
									                    $rssresult = run("weblogs:rss:publish", array($uid, false));
									                    $rssresult = run("files:rss:publish", array($uid, false));
									                    $rssresult = run("profile:rss:publish", array($uid, false));
									                    
									                    $f = new StdClass;
									                    $f->owner = $uid;
									                    $f->friend = $USER->ident;
									                    insert_record('friends',$f);
									                    $messages[] = gettext("Your user was created and you were added as its first friend.");
									                    
													}
												}
												
											}
											break;
			// Move an owned user
				case "owneduser:move":
										if (logged_on
											&& isset($_REQUEST['owner'])
											&& isset($_REQUEST['profile_id'])
											&& isset($_REQUEST['newowner'])) {
												$originalowner = optional_param('owner',0,PARAM_INT);
												if ($owneduser = get_record('users','ident',optional_param('profile_id',0,PARAM_INT))) {
													if (($owneduser->owner == $USER->ident) || run("users:flags:get", array("admin", $_SESSION['userid']))) {
														$owneduser->owner = optional_param('newowner',0,PARAM_INT);
														update_record('users',$owneduser);
														execute_sql("update {$CFG->prefix}weblog_posts set access='user".$owneduser->owner."' where access='user".$originalowner."' and owner=".$owneduser->ident,false);
														execute_sql("update {$CFG->prefix}weblog_comments set access='user".$owneduser->owner."' where access='user".$originalowner."' and owner=".$owneduser->ident,false);
														execute_sql("update {$CFG->prefix}files set access='user".$owneduser->owner."' where access='user".$originalowner."' and owner=".$owneduser->ident,false);
														execute_sql("update {$CFG->prefix}tags set access='user".$owneduser->owner."' where access='user".$originalowner."' and owner=".$owneduser->ident,false);
														execute_sql("update {$CFG->prefix}profile_data set access='user".$owneduser->owner."' where access='user".$originalowner."' and owner=".$owneduser->ident,false);
														if (! record_exists('friends','ident',$owneduser->ident,'friend',$owneduser->owner)) {
															$f = new StdClass;
										                    $f->owner = $owneduser->ident;
										                    $f->friend = $owneduser->owner;
										                    insert_record('friends',$f);
														}
														$messages[] = $owneduser->username . gettext(" successfully moved."); // gettext variable
													} else {
														$messages[] = gettext("You do not own this user."); // gettext variable
													}
												} else {
													$messages[] = gettext("The user does not exist."); // gettext variable
												}				
											}
											break;
								
				
			}
			
		}

?>