<?php
/**
* This page is used to delete a page.
* Deleting a page requires that the user be logged in and the owner of a personal wiki page, or a member of
*       a community.
*
* @package folio
* @param string $page The page title being deleted.  Required.
* @param string $user The user/community to whom this page belongs.
**/

    define("context", "folio_page_delete");
    // Run includes
    require_once("../includes.php");
    require_once("../mod/folio/control/page_delete.php");
	
	$title = " Wiki :: Delete Page";

	// Retrieve page name & user name.
	// 	REQUIRED BY THE MENU SYSTEM (mod/folio/lib.php)
	$page_title = folio_page_decodetitle( required_param('page') );
	$username = required_param('user');
	$page_owner = run('users:name_to_id', $username);
	$profile_id = $page_owner;

    
    // Get the $page_ident where page & user matches.
    $page_ident = folio_page_translatetitle($username, $page_title);
    $page = folio_page_select( $page_ident );
    
    // Validate permissions.  This also verifies to see if we have access to create a page.
    $permissions = folio_page_security_select( $page_ident );
    
    if ( !folio_page_permission($page, $permissions, 'delete') ) {
    
        // User doesn't have permission to delete the page.
        $body = 'You do not have permission to delete this page.  ' .
            'You must be logged in, be deleting your own wiki page, or be a member of the community owning this page.';
        $function['display:sidebar'] = array();
        
    } elseif ( folio_page_is_homepage( $page ) ) {
        // Can't delete homepage
        $body = 'Sorry, but you can not delete a homepage.';
    } else {
        // User does have permission to delete the page.
    	
    	// Build the html controls for the page.
    	$body = folio_page_delete($page, $page_title, $username);
	
        // Reset the side menu after defining the comment on variables.
        $function['display:sidebar'] = array('');
    }

    header("Cache-control: private");
    $body = templates_draw(array(
                    'context' => 'contentholder',
                    'title' => $title,
                    'body' => $body
                )
                );
    
    echo templates_page_draw(array(
                    $title, $body
                )
                );
                        
?>