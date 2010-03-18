<?php
global $CFG;
global $messages;
global $USER;

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

// Actions to perform on the owned users activity screen

if (isset($_REQUEST['action'])) {
	switch($_REQUEST['action']) {
		
		// Make a comment visible
		case "owneduser_comment_public":
			if (logged_on
				&& isset($_REQUEST['comment_ident'])) {
					$comment_id = $_REQUEST['comment_ident'];
		            $commentinfo = get_record_sql('SELECT wc.*,wp.owner AS postowner,wp.ident AS postid
		                                           FROM '.$CFG->prefix.'weblog_comments wc 
		                                           LEFT JOIN '.$CFG->prefix.'weblog_posts wp ON wp.ident = wc.post_id
		                                            WHERE wc.ident = ' . $comment_id);
		            if ($commentinfo->owner == $USER->ident || run("permissions:check", "weblog")) {
			            if (set_field('weblog_comments','access','PUBLIC','ident',$comment_id)) {
			                if ($post = get_record_select('weblog_posts','ident = '.$commentinfo->post_id)) {
					        	if (run("users:flags:get",array("emailreplies",$post->owner))) {
					                if ($email = get_record('users','ident',$post->owner)) {
					                    $username = $email->username;
					                    $message = gettext(sprintf("You have received a comment from %s on your blog post '%s'. It reads as follows:", $commentinfo->postedname, stripslashes($post->title)));
					                    $message .= "\n\n\n" . stripslashes(html_entity_decode(strip_tags($commentinfo->body),ENT_QUOTES)) . "\n\n\n";
					                    $message .= gettext(sprintf("To reply and see other comments on this blog post, click here: %s", url . $username . "/weblog/" . $post->ident . ".html"));
					                    $message = wordwrap($message);
					                    $from = new StdClass;
					                    $from->email = $CFG->noreplyaddress;
					                    $from->name = $comment->postedname;
					                    @email_to_user($email,$from,stripslashes($post->title),$message);
					                }
					            }
				            }
		                	print("Comment updated");
		                }
		                else print("Error updating comment");
		            }
				}
				break;
		// Delete comment
		case "owneduser_comment_delete":
				if (logged_on
					&& isset($_REQUEST['comment_ident'])) {
						$comment_id = $_REQUEST['comment_ident'];
			            $commentinfo = get_record_sql('SELECT wc.*,wp.owner AS postowner,wp.ident AS postid
			                                           FROM '.$CFG->prefix.'weblog_comments wc 
			                                           LEFT JOIN '.$CFG->prefix.'weblog_posts wp ON wp.ident = wc.post_id
			                                            WHERE wc.ident = ' . $comment_id);
			            if ($commentinfo->owner == $USER->ident || run("permissions:check", "weblog")) {
			                if (delete_records('weblog_comments','ident',$comment_id)) print("Comment deleted");
			                else print("Error deleting comment");
			            }
					}
					break;
	}
	
}
?>