<?php
/**
* This is called by pages doing a POST.  It will process the action data passed, and then redirect.
* 
* @package folio
* @param string $action The type of action to perform.  Executes code in folio_actions.php
* @param string $redirect_url The redirected location.
**/
	define("context", "redirect");
	
	// Load libraries.
	require_once('../includes.php');

    run("profile:init");
    run("friends:init");
    run("folio:init");
	
// I'm not sure if these are needed.
// todo Find out if these are required.
global $redirect_url;
global $messages;
global $page_owner;

// Test to make sure that we've got an action tag.
if (optional_param('action', '-1') == '-1') {
	// None passed, quit.
	error('No action paramater was passed to _folio/action_redirection.php');
	die();
}

// Run the required actions.
if ( required_param ( 'action' ) == 'folio:page:update' ) {
	// Include the _post files for the page update controls.  Need to include security one first, as it's output var $security_ident
	//	is used by the page_edit_post.php file.  Note that page_edit_post also updates $redirect_url to give us the proper output.
    
    // Test for dup names
    if ( folio_duplicate_pagename( $_POST['user_ident'], $_POST['page_title'], $_POST['page_ident'] ) ) {
        error('A page by the name of "' . $_POST['page_title'] . '" already exists. Please choose another name for page ' .
            $_POST['page_ident'] . ' and try to save again.');
        die();
    } else {
        require_once('../mod/folio/control/page_edit_security_post.php');
        require_once('../mod/folio/control/page_edit_post.php');
    }
} elseif ( required_param ( 'action' ) == 'folio:page:delete' ) {
    // Delete a page.
    require_once('../mod/folio/control/page_delete_post.php');

} else {
	// No valid action passed.
	error('No valid action passed: ' . required_param('action') );
}

if (isset($messages) && sizeof($messages) > 0) {
    $_SESSION['messages'] = $messages;
}

// Redirect.
if (isset($redirect_url)) {
    header("Location: " . $redirect_url);
} else {
	error('No redirect_url passed to _folio/action_redirection.php.' . $redirect_url);
	die();
}

?>