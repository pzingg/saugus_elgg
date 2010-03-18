<?php
/**
* View the history of changes for a single page
*
* @package folio
* @param string $user The user/community to which this page belongs.  
* @param string $page The title of the page being shown.
*/    
    define("context", "folio_page_history");
    // Run includes
    require("../includes.php");
	require_once("../mod/folio/control/pagelist.php");
	require_once("../mod/folio/control/breadcrumb.php");
	
    run("profile:init");
    run("friends:init");
    run("folio:init");
	
	// Retrieve passed variables (page name & user name, as well as created).
	// 	These variables will be used by the menu system to build links.  See lib.php for more details.
	//	Note that the included side menu depends upon these variable names.
	$page_title = folio_page_decodetitle( optional_param('page', '') );
	$username = required_param('user');
	$page_owner = run('users:name_to_id', $username);
	$url = url;
	
	// Test to see if this is a valid user.
	if ( !$page_owner ) {
		// Nothing returned by the run command, not a valid user.  
		error( 'Sorry, but "' . $username . '" is not a valid username in this system.');
		die();
	} else {
		$profile_id = $page_owner;
		$name = run('users:display:name', $page_owner);
	}
	
	// Look to see if a page param was passed.  If not, find out what page
	//	is set as this user's default homepage.  If so, translate title into
	//	a page_ident.
	if ( $page_title == '' ) {
		// Because the home page can have its name changed, search to pull up the correct record.
		$page = folio_homepage( $username );

		if ( $page ) {
			$page_title = $page->title;
			$page_ident = $page->page_ident;
		} else {
			// Homepage hasn't been created yet.
			$page_title = 'Home Page';
			$page_ident = -1;
            error('You can not view history for the Homepage when it has not yet been created');
		}
	} else {
		// Transate the page & user vars into an int $page_ident
		$page_ident = folio_page_translatetitle($username, $page_title);
	}
		

	// If we found a matching page, retrieve record & permissions.
	if ($page_ident <> -1) {
		$page = folio_page_select( $page_ident );
		$permissions = folio_page_security_select( $page_ident );
	} else {
		$page = false;
		$permissions = false;
	}

	// Test to see if we have permissions
	$ok = folio_page_permission( $page, $permissions, 'read', $profile_id );

	// If we have permissions to view the page, then continue loading controls.
	//	Also has the side effect of not loading other controls if the page hasn't been 
	//	created yet.
	if ( $ok && $page) {
        // Run the command to actually retrieve the content.  
        $body = 
            folio_control_historypagelist( $username, $page, $page_owner );
			
	    // Reset the side menu after defining the comment on variables.
	    $comment_on_type= 'page';
	    $comment_on_ident= $page_ident;
		$comment_on_username = $username;
		$comment_on_name = $name;
		$comment_on_ident = $page_owner;

        // Build nice titles
        $html_title = "<a href='$url{$username}/subscribe/rss/page+page_comment/'><img border=0 src='{$url}_templates/icons/rss.png' /></a> $name : " . folio_control_breadcrumb( $page, $username ) ;
        $plain_title = $page->title;

    } elseif ( $ok & !$page ) {
        // Page hasn't been created yet.
		$html_title = 'Error: Can not view history for an uncreated page';
    	$plain_title = 'Error: Can not view history for an uncreated page';
        $body = 'You can not view history for a page that does not exist.';
        
	} else {
    	// We don't have permissions, and so need some sort of title.
		$html_title = 'You do not have permission to view this page';
    	$plain_title = 'You do not have permission to view this page';
        $body = 'You do not have permission to view this page. Please contact the page\'s' .
				' owner and ask for the security to be set to <b>Public</b> or <b>Moderated</b>. ' .
				'  You will be able to view this page once that has been done.  If this page belongs ' .
                ' to a community, you may also try to join the community.  Once you are a member, ' . 
                ' you will be able to see and edit the page.';

    }

    // Transfer into template & write.
    templates_page_setup();
    $body = templates_draw(array(
                    'context' => 'contentholder',
                    'title' => $html_title,
                    'body' => $body
                )
                );
    
    echo templates_page_draw(array(
                    $plain_title, $body
                )
                );

?>