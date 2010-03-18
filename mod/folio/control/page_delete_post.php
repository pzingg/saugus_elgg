<?php
/**
* Function: 
*	This is the postback page for the page_delete.php control.
*	The html control output from that page should be passed to this file for processing.
*
* @todo Upon converting to AJAX poststyle (if done), look into loading a lighter version of includes.php.
*
* @package folio
* @uses includes.php
* @uses $CFG
* @param int $page_ident  The identity key for the page.
**/

	// Note, this is ../ insead of ../../../ because we're called by files in /_folio, and not from the folder that this 
	//	page is actually residing inside of.
	require_once('../includes.php');
    $url = url;

	// Load variables
	$page_ident = required_param('page_ident', PARAM_INT);
	
    //	Get the record security and page information.
    $page = folio_page_select( $page_ident );
    $permissions = folio_page_security_select ( $page_ident );
	    
    if ( !$page ) {
        error ( "Invalid page $page_ident passed to page_delete_post");
    }
    
    if ( isloggedin() & folio_page_permission($page, $permissions, 'delete') ) {
        // Permission to delete granted.
        
        // Turn all records with the same page_ident and newest=1 to newest = 0, effectively deleting the page.
        set_field('folio_page', 'newest', 0, 
            'page_ident', $page_ident, 'newest', 1);
    } else {
        var_dump( $page );
        var_dump( $permissions );
        error('You do not have permission to delete this page');
    }

    global $redirect_url;
    $username = run('users:id_to_name', $page->user_ident);
    $redirect_url = url . $username . '/page/';
    
    // Create 'delete' rss record.
    rss_additem( $page->user_ident, $username, 
        $_SESSION['userid'], $_SESSION['name'], $_SESSION['username'], 
        'page', $page->page_ident, 
        '', 'Deleted ' . $page->title , 
        $_SESSION['username'] . ' deleted the page "' . $page->title . '"', 
        $url . $username . '/page/', 
        'PUBLIC' );
?>