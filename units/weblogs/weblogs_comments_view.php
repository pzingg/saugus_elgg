<?php
/*
 * Created on Sep 14, 2006 - JKlein
 *
 * Needed to add a comments list generator so that we can implement access control
 */
 
 if (isset($parameter)) {
        
    $post = $parameter;
    
    global $post_authors;
    global $individual;
    global $CFG;
    global $USER;
    
    $run_result = "";
    $commentsbody = "";
 
 	$where = run("users:access_level_sql_where",$_SESSION['userid']);
 	if (logged_on && $USER->owner != -1 && $post->owner == $USER->ident) $where .= " or owner=" . $USER->owner . " ";
 	if (logged_on && $USER->owner == -1 && $CFG->owned_users_allaccess && get_field("users","owner","ident",$post->owner) != -1)
    	$where .= " or 0=0 ";
	$comments = get_records_select('weblog_comments','('.$where.') AND post_id = '.$post->ident,null,'posted ASC');

	if (!empty($comments)) {
        foreach($comments as $comment) {
        	if (run("permissions:check",array("weblog:comment",$comment,$post))) {
	            $commentmenu = "";
	            if (logged_on && ($comment->owner == $_SESSION['userid'] || $comment->access == "user".$_SESSION['userid'] || run("permissions:check", "weblog:comment"))) {
	            	$Edit = "";
	            	$Delete = "";
	                $returnConfirm = gettext("Are you sure you want to permanently delete this weblog comment?");
	                if (($comment->access != "PUBLIC") && ($USER->owner == -1))
	                	$Edit = "[<a href=\"".$CFG->wwwroot."_weblog/action_redirection.php?action=weblog_comment_public&amp;weblog_comment_public=".$comment->ident."\">".gettext("Make visible")."</a>]";
	                $Delete = "[<a href=\"".$CFG->wwwroot."_weblog/action_redirection.php?action=weblog_comment_delete&amp;weblog_comment_delete=".$comment->ident."\" onclick=\"return confirm('".$returnConfirm."')\">".gettext("Delete")."</a>]";
	                $commentmenu = <<< END
	
	        <p>
	                $Edit $Delete
	        </p>
END;
	            }
	            $comment->postedname = htmlspecialchars($comment->postedname, ENT_COMPAT, 'utf-8');
	            
	            // turn commentor name into a link if they're a registered user
	            // add rel="nofollow" to comment links if they're not
	            if ($comment->owner > 0) {
	                $commentownerusername = run("users:id_to_name",$comment->owner);
	                $comment->postedname = '<a href="' . url . $commentownerusername . '/">' . $comment->postedname . '</a>';
	                $comment->icon = '<a href="' . url . $commentownerusername . '/">' . "<img src=\"" . $CFG->wwwroot . $commentownerusername . "/icons/" . run("icons:get",$comment->owner) . "/w/50/h/50/\" border=\"0\" align=\"left\" alt=\"\" /></a>";
	                $comment->body = run("weblogs:text:process", array($comment->body, false));
	            } else {
	                $comment->icon = "<img src=\"" . $CFG->wwwroot . "_icons/data/default.png\" width=\"50\" height=\"50\" align=\"left\" alt=\"\" />";
	                $comment->body = run("weblogs:text:process", array($comment->body, true));
	            }
	            
	            $commentsbody .= templates_draw(array(
	                                                  'context' => 'weblogcomment',
	                                                  'postedname' => $comment->postedname,
	                                                  'body' => $title = get_access_description($comment->access).$comment->body . $commentmenu,
	                                                  'posted' => strftime("%A, %e %B %Y, %R %Z",$comment->posted),
	                                                  'usericon' => $comment->icon
	                                                  )
	                                            );
        	}
            
        }
        $commentsbody = templates_draw(array(
                                             'context' => 'weblogcomments',
                                             'comments' => $commentsbody
                                             )
                                       );
        
    }
            
    $run_result = $commentsbody;   
 
 }
 
?>
