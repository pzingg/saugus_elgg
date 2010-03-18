<?php
/**
* Function: 
*	This is the postback page for the page_edit.php control.
*	The html control output from that page should be passed to this file for processing.
* Requirements:
*	The page_edit_security_post.php should be called first, as that file will set the $security_ident variable.
* Actions:
* 	Set any old records with matching ids to not current.
*	Insert a new record.
* Side Effects:
*	Sets the $redirect_url variable to url/username/page/pagetitle.
* Notes:
*	This doesn't create/modify security records.  The page_edit_security_post file should do that, and is called first.
*
* @todo CRITICAL TODO: ADD SECURITY CHECK TO VERIFY USER HAS PERMISSIONS TO EDIT PAGE.
* @todo Upon converting to AJAX poststyle (if done), look into loading a lighter version of includes.php.
*
* @uses includes.php
* @package folio
* @param int $security_ident The security setting value for the page, needs to be set by page_edit_security_post, not thru $_POST.
* @param array $page_ident  The identity key for the page.
* @param string $page_title The new title for the page.
* @param string $page_body The new body for the page.
* @param string $username The username for the page's owner.
* @param int $user_ident The ident key for the page's owner.
* @param string $redirect_url OUTPUT.  VIP -- used by action_redirect to move to the proper page.
**/

	// Note, this is ../ insead of ../../../ because we're called by files in /_folio, and not from the folder that this 
	//	page is actually residing inside of.
	require_once('../includes.php');

	// The security ident needs to be set by page_edit_security_post before this can run.
	if (!isset($security_ident) ) {
		error('Page_Edit_post.php must be called after page_edit_security_post so that the former knows the security information');
	}

	$page = new StdClass;
	$page->title = folio_clean( required_param('page_title') );
	$page->body = required_param('page_body');
	$page->page_ident = required_param('page_ident', PARAM_INT );
	$page->security_ident = $security_ident;
	$page->parentpage_ident = required_param('parentpage_ident', PARAM_INT );
	$page->newest = 1;
	$page->created = time();
	$page->user_ident = required_param('user_ident');
	$username = required_param('username');
	
	// If the user isn't logged in, then set to -1 (anonymous)
	// Otherwise, set the last updater to their logged in information.
	if ( isloggedin() ) {
		$page->creator_ident = $_SESSION['userid'];	
	} else {
		$page->creator_ident = -1;	
	}
	
	// Modify old record by converting all records matching the conditions (folio, page, newest) to newest=false.
	set_field('folio_page', 'newest', 0, 'newest', 1, 'page_ident', $page->page_ident);
		
    //var_dump( $page );
	// Insert new record into db.
	$insert_id = insert_record('folio_page',$page, true, 'page_ident');
	
	// Set redirect
	$redirect_url = url . $username . '/page/'.  folio_page_encodetitle( required_param('page_title') );
	
	// Create RSS record
	rss_additem( $page->user_ident, $username,
		$page->creator_ident, $_SESSION['name'], $_SESSION['username'], 
		'page', $insert_id, 
		'', $page->title, 
		folio_page_makelinks($username, $page->body), 
		$redirect_url,
        optional_param('folio_control_page_edit_security_custom', 'PUBLIC') 
        );
	
?>