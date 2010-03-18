<?php
	global $CFG;
	global $page_owner;
	global $USER;
	
	if ($page_owner != -1) {
		if (run("users:type:get", $page_owner) == "person") {
			
			if ($result = get_records_select('users',"owner = ? AND user_type = ?",array($page_owner,'person'))) {
				$body = "<ul>";
				$username = run("users:id_to_name",$page_owner);
				if (logged_on && $page_owner == $USER->ident) {
					$body .= "<li><a href=\"".$CFG->wwwroot."_ownedusers/unapproved.php\">".gettext("View ".$CFG->owned_users_caption."' activity") . "</a><br /></li>";
				}
				$body .= "<li><a href=\"".$CFG->wwwroot.$username."/weblog/ownedusers\">".$CFG->owned_users_caption."' Blog</a></li>";
				$body .= "<li><a href=\"".$CFG->wwwroot."_ownedusers/owned.php?owner=".$page_owner."\">View all ".$CFG->owned_users_caption."</a></li>";
				$body .= "</ul>";
				
				$run_result .= "<li id=\"users_owned\">";
	            $run_result .= templates_draw(array(
	                                                'context' => 'sidebarholder',
	                                                'title' => gettext($CFG->owned_users_caption),
	                                                'body' => $body
	                                                )
	                                          );
	            $run_result .= "</li>";
			} else {
				$run_result .= "";
			}
		}
	}
	
?>