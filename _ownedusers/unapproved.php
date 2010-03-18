<?php
global $CFG;
	//	ELGG recent activity page

	// Run includes
	require_once(dirname(dirname(__FILE__))."/includes.php");
		
	// Initialise functions for user details, icon management and profile management
		run("profile:init");

	// Whose friends are we looking at?
		global $page_owner;
		
	// Weblog context
		define("context", "weblog");
		
	// You must be logged on to view this!
		protect(1);
		
		templates_page_setup();
		
		$title = run("profile:display:name") . " :: ".$CFG->owned_users_caption." recent activity";

	// If we haven't specified a start time, start time = 1 day ago
	
		if (!isset($_REQUEST['starttime'])) {
//			$starttime = time() - 86400;
			$starttime = $USER->last_action; //show activity since last login by default
		} else {
			$starttime = (int) $_REQUEST['starttime'];
		}
		
		$body = "<p>" . gettext("Currently viewing ".$CFG->owned_users_caption." activity since ") . (($starttime == $USER->last_action) ? "last login (". gmdate("F d, Y",$starttime).")" : gmdate("F d, Y",$starttime)) . ".</p>";
		
		$body .= "<p>" . gettext("You may view ".$CFG->owned_users_caption." activity during the following time-frames:") . "</p>";
		
		$body .= "<ul><li><a href=\"unapproved.php?starttime=" . $USER->last_action . "\">" . gettext("Since last login") . " (". gmdate("F d, Y",$starttime).")</a></li>";
		$body .= "<li><a href=\"unapproved.php?starttime=" . (time() - 86400) . "\">" . gettext("The last 24 hours") . "</a></li>";
		$body .= "<li><a href=\"unapproved.php?starttime=" . (time() - (86400 * 2)) . "\">" . gettext("The last 48 hours") . "</a></li>";
		$body .= "<li><a href=\"unapproved.php?starttime=" . (time() - (86400 * 7)) . "\">" . gettext("The last week") . "</a></li>";
		$body .= "<li><a href=\"unapproved.php?starttime=" . (time() - (86400 * 30)) . "\">" . gettext("The last month") . "</a></li></ul>";
		
		//weblog posts
		$body .= "<h2>".gettext("Weblog Posts:")."</h2>";		
		if ($activities = get_records_sql('SELECT u.username, wp.ident AS weblogpost, wp.title AS weblogtitle, wp.weblog AS weblog
				 FROM '.$CFG->prefix.'weblog_posts wp LEFT JOIN '.$CFG->prefix.'users u ON u.ident = wp.weblog WHERE wp.posted >= ? AND
				 wp.owner IN (SELECT ident FROM '.$CFG->prefix.'users WHERE owner = ? AND user_type = ?) AND wp.access = ?
				 ORDER BY wp.posted DESC',array($starttime,$page_owner,'person','user'.$page_owner))) {
			foreach($activities as $activity) {
				$posttitle = stripslashes($activity->weblogtitle)."  [<a href=\"". $CFG->wwwroot . $activity->username . "/weblog/" . $activity->weblogpost . ".html\">" . gettext("Review") . "</a>]";
				$poster = sprintf(gettext("<b>$activity->username</b>:"));
				$body .= templates_draw(array(
									'context' => 'databox1',
									'name' => $poster,
									'column1' => $posttitle
								)
								);
			}
		} else {
			$body .= "<p>" . gettext("No activity during this time period.") . "</p>";
		}
		
		
		//Comments
		$body .= "<h2>" . gettext("Comments on weblog posts") . "</h2>";
		$body .= "<script src=\"ajaxscript.js\" type=\"text/javascript\"></script><div id=\"err\"></div>";

		// TODO: the foreach loops are identical and could possibly do with being functionised - Sven
		
		if ($activities = get_records_sql('SELECT wc.*, u.username,u.name as weblogname, wp.ident AS weblogpost,wp.title AS weblogtitle, wp.weblog AS weblog
		                                    FROM '.$CFG->prefix.'weblog_comments wc
		                                    LEFT JOIN '.$CFG->prefix.'weblog_posts wp ON wp.ident = wc.post_id
		                                    LEFT JOIN '.$CFG->prefix.'users u ON u.ident = wp.weblog 
		                                    WHERE wc.posted >= ? AND wp.owner <> ? AND wc.access = ?
		                                    ORDER BY wc.posted DESC',array($starttime,$page_owner,'user'.$page_owner))) {
		    
		    foreach($activities as $activity) {
		    	$commentbody = "<div id=\"comment".$activity->ident."\">";
		        $commentbody .= stripslashes($activity->body);
		        $commentbody .= "<br /><br />[<a href=\"" . url . $activity->username . "/weblog/" . $activity->weblogpost . ".html\">" . gettext("Read more") . "</a>] ";
		        $commentbody .= "[<a class=\"actionLink\" href=\"" . url . "_ownedusers/action_redirection.php?action=owneduser_comment_public&comment_ident=" . $activity->ident . "&starttime=".$starttime."\">" . gettext("Make Visible") . "</a>] ";
		        $commentbody .= "[<a class=\"actionLink\" href=\"" . url . "_ownedusers/action_redirection.php?action=owneduser_comment_delete&comment_ident=" . $activity->ident . "&starttime=".$starttime."\">" . gettext("Delete") . "</a>]";
		        $commentbody .= "</div>";
		        $activity->postedname = stripslashes($activity->postedname);
		        $activity->weblogname = stripslashes($activity->weblogname);
		        if ($activity->weblog == $USER->ident) {
		            $activity->weblogname = gettext("your blog");
		        }
		        if ($activity->owner == $USER->ident) {
		            $commentposter = sprintf(gettext("<b>You</b> commented on weblog post '%s' in %s:"), stripslashes($activity->weblogtitle), $activity->weblogname);
		        } else {
		            $commentposter = sprintf(gettext("<b>%s</b> commented on weblog post '%s' in %s:"), $activity->postedname, stripslashes($activity->weblogtitle), $activity->weblogname);
		        }
		        $body .= templates_draw(array(
		                                        'context' => 'databox1',
		                                        'name' => $commentposter,
		                                        'column1' => $commentbody
		                                      )
		                                );
		    }
		} else {
		    $body .= "<p>" . gettext("No activity during this time period.") . "</p>";
		}
		
		
		//Profile Updates
		$body .= "<h2>".gettext("Updated Profiles:")."</h2>";
		if ($activities = get_records_sql('SELECT u.username, pd.name, pd.owner FROM '.$CFG->prefix.'profile_data pd
			LEFT JOIN '.$CFG->prefix.'users u ON u.ident = pd.owner WHERE pd.owner IN (SELECT ident FROM '.$CFG->prefix.'users
			WHERE owner = ? AND user_type = ?) AND pd.access = ? ORDER BY pd.owner',array($page_owner,'person','user'.$page_owner))) {
			$priorid = "";
			$profilebody = "";
			$profileposter = "";
			foreach($activities as $activity) {
				if ($activity->owner != $priorid && $priorid != "") {
					$profilebody .= "  [<a href=\"" . $CFG->wwwroot . "profile/edit.php?profile_id=" . $priorid . "\">" . gettext("Review") . "</a>]";
					$body .= templates_draw(array(
									'context' => 'databox1',
									'name' => $profileposter,
									'column1' => $profilebody
								)
								);
					$profilebody = "";
				}
				$profilebody .= stripslashes($activity->name)." ";
				$profileposter = sprintf(gettext("<b>$activity->username</b>:"));
				$priorid = $activity->owner;
			}
			$profilebody .= "  [<a href=\"" . $CFG->wwwroot . "profile/edit.php?profile_id=" . $activity->owner . "\">" . gettext("Review") . "</a>]";
			$body .= templates_draw(array(
							'context' => 'databox1',
							'name' => $profileposter,
							'column1' => $profilebody
						)
						);
		} else {
			$body .= "<p>" . gettext("No activity at this time.") . "</p>";
		}
		
		$body = templates_draw(array(
						'context' => 'contentholder',
						'title' => $title,
						'body' => $body
					)
					);
		
		echo templates_page_draw( array(
					$title, $body
				)
				);

?>