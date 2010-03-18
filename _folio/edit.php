<?php
/**
* This page is used to edit a page's text and/or settings.
* It can also be used to add a new page thru 2 different methods. 
*	(a) Explicit.  Called with param new=T. In this case, place as a child page of the passed page title.
*	(b) Implicit.  Called by clicking on a [[NewPage]] link.  In this case, we already have the new page's
*		title, but have to assume that it should be placed under the homepage in the root namespace.
*
* @package folio
* @param string $page The page title being edited.  Required.
* @param string $user The user/community to whom this page belongs.
* @param string $new Default = F. Are we adding a new page under the passed page? T if yes.
*	Note: We can not depend on new being T for all new pages, see (a) Implicit option above.
**/

    define("context", "folio_page_edit");
    // Run includes
    require_once("../includes.php");
    require_once("../mod/folio/control/page_edit.php");
	
    run("profile:init");
    //run("friends:init");
    //run("folio:init");
	
	$title = " Wiki :: Edit Page";

	// Retrieve page name & user name.
	// 	REQUIRED BY THE MENU SYSTEM (mod/folio/lib.php)
	$page_title = folio_page_decodetitle( required_param('page') );
	$username = required_param('user');
	$new = optional_param('new', 'F');
	$page_owner = run('users:name_to_id', $username);
	$profile_id = $page_owner;

    
    // Get the $page_ident where page & user matches.
    $page_ident = folio_page_translatetitle($username, $page_title);
    $page = folio_page_select( $page_ident );
    
    // Validate permissions.  This also verifies to see if we have access to create a page.
    $permissions = folio_page_security_select( $page_ident );
    
    if ( !folio_page_permission($page, $permissions, 'write', $profile_id) ) {
    
        // User doesn't have permission to write the page.
        $body = 'You do not have permission to edit this page.';
        //not sure why he wanted to burn the sidebar, putting it back - JK
        //$function['display:sidebar'] = array();
        
    } else {
        // User does have permission to edit the page.
    	
    	if ($new == 'T' ) {
    		// Adding a new page as a child of the page title passed.
    		//	Default parentpage to the passed page title.
    		if ( $page_ident == -1 ) {
    			error( 'Could not find page ' . $page_title . ' for user ' . $username . ' to add a page under.' .
    				' Exception thrown by _folio/edit.php');
    		}
    		$parentpage_ident = $page_ident;
    		$page_title = 'New Page';
    		$page = false;
    		$permissions = false;
    	} elseif ($page_ident <> -1) {
    		// If we found a matching page, retrieve parent ident.	
    		$parentpage_ident = $page->parentpage_ident;
    	} else {
    		// No matches. We're creating a new page from someone clicking on a [[new page link]],
    		//	or are creating the homepage for the first time.  Always make parentpage = homepage.
    		$page = false;
    		$permissions = false;
    		$parentpage_ident = folio_homepage( $username );
    		
    		if ( !$parentpage_ident ) {
    			// Unable to find homepage.  Initialize.
    			$parentpage_ident = folio_homepage_initialize( $username );
    		} else {
    			// Found homepage.
    			$parentpage_ident = $parentpage_ident->page_ident;
    		}
    		
    		if ( $page_title == 'Home Page' ) {
    			// Creating the homepage for the first time.
    			$page_ident = $parentpage_ident;
    			$page = folio_page_select( $parentpage_ident );
    			$permissions = folio_page_security_select( $page_ident );
    		}
    	}
    	
    	// Build the html controls for the page.
    	$body = folio_page_edit($page, $permissions, $page_title, $username, $parentpage_ident);
	
        // Reset the side menu after defining the comment on variables.
        $comment_on_type= 'page';
        $comment_on_ident= $page_ident;
        //not sure why he wanted to burn the sidebar, putting it back - JK
        //$function['display:sidebar'] = array(path . "mod/folio/page_edit_menu.php");
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