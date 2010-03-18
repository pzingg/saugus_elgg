<?php

    //    ELGG owned user activity perform-action-then-redirect page

    // Run includes
    require_once(dirname(dirname(__FILE__))."/includes.php");
        
    run("profile:init");

    global $redirect_url;
    global $messages;
	global $USER;
	global $page_owner;
	global $CFG;

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
					                set_field('weblog_comments','access','PUBLIC','ident',$comment_id);
					                $messages[] = gettext("Comment was made public.");
					            }
					            $redirect_url = url . "_ownedusers/unapproved.php";
					            if (isset($_REQUEST['starttime'])) $redirect_url .= "?starttime=".$_REQUEST['starttime'];
					            define('redirect_url',$redirect_url);
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
					                delete_records('weblog_comments','ident',$comment_id);
					                $messages[] = gettext("Comment was deleted.");
					            }
					            $redirect_url = url . "_ownedusers/unapproved.php";
					            if (isset($_REQUEST['starttime'])) $redirect_url .= "?starttime=".$_REQUEST['starttime'];
					            define('redirect_url',$redirect_url);
							}
							break;
				
			}
			
		}
	if (isset($messages) && sizeof($messages) > 0) {
        $_SESSION['messages'] = $messages;
    }
        
    if (defined('redirect_url')) {
        header("Location: " . redirect_url);
    } else {
        header("Location: " . url);
    }
        
?>