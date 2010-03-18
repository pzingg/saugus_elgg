<?php
/**
* View a single page
*  Shows history pages.  Note that this file uses rewritten urls set in .htaccess
*
* @package folio
* @param string $user The user/community to which this page belongs.  
* @param string $page_ident The ident key of the page being shown.
* @param string $revert (T/F) If we're trying to revert the page to an older version.
* @param int $created Used to specify that we're showing an older version of the page (optional = -1)
*/    
    define("context", "folio_page_oldview");
    // Run includes
    require("../includes.php");
	require_once("../mod/folio/control/breadcrumb.php");
    require_once("../mod/folio/control/page_view.php");
    require_once("../mod/folio/control/page_edit.php");
	
    run("profile:init");
    run("friends:init");
    run("folio:init");
	
	// Retrieve passed variables (page name & user name, as well as created).
	// 	These variables will be used by the menu system to build links.  See lib.php for more details.
	//	Note that the included side menu depends upon these variable names.
    $page_ident = required_param('page_ident', PARAM_INT);
	$username = required_param('user');
    $created = required_param('created',PARAM_INT);
    $revert = optional_param('revert','F');
	$page_owner = run('users:name_to_id', $username);
	$url = url;
    
	// Test to see if this is a valid user.
	if ( !$page_owner) {
		// Nothing returned by the run command, not a valid user.  
		error( 'Sorry, but "' . $username . '" is not a valid username in this system.');
		die();
	} else {
		$profile_id = $page_owner;
		$name = run('users:display:name', $page_owner);
	}

    // Initialize page & permissions records to false.
    $page = false;
    $permissions = false;

    // A page ident was passed.  Pull up records & set page_title variable.
    $page = folio_page_select( $page_ident, $created );
    $permissions = folio_page_security_select( $page_ident );
    

    // Pull up the title of the current version of the page.
    //      Note that we use title instead of the one of the older version of page, as all of the 
    //      various pagelists, comments, etc.. all pull off of the $page_title variable.
    $currentpage = folio_page_select( $page_ident, -1 );
    $page_title = $currentpage->title;
    
	// Test to see if we have permissions
	$ok = folio_page_permission( $page, $permissions, 'read', $profile_id );
	$ok_write = folio_page_permission( $page, $permissions, 'write', $profile_id );

	// If we have permissions to view the page, then continue loading controls.
	//	Also has the side effect of not loading other controls if the page hasnt' been 
	//	created yet.
	if ( $ok ) {

        // Build nice titles
        $html_title = "<a href='$url{$username}/subscribe/rss/page+page_comment/'><img border=0 src='{$url}_folio/images/xml.gif' /></a> $name : " . folio_control_breadcrumb( $page, $username ) ;
        $plain_title = $page->title;

        // See if we're trying to revert the page. Need to check security.
        if ( $revert == 'T' && $ok_write) {
            // Revert
            
            $body = "<p align='center'><b>Restore this older version of the page.</b></p><br/>" .
                folio_page_edit($page, $permissions, $page_title, $username, $page->parentpage_ident);
        
        } else {
            // Just viewing.
        
            // Build warning if this is an older version of the page.
            if ( $page->newest != 1 ) {
                // Create warning link
                $body = "<p align='center'><b>You are looking at an archived version of this page.</b><br/>" .
                    "<a href='{$url}{$username}/page/" . folio_page_encodetitle($page_title) . 
                    "'>return to current version</a>";
                if ($ok_write) $body .= " :: " .
                    "<a href='{$url}{$username}/page/{$page_ident}:{$created}/revert'>restore this version</a></p>\n";
                    
            } else {
                $body = '';
            }
        
            // Run the command to actually retrieve the content.  
            $body .= 
                folio_page_view($page_ident,$page_title, $created, $page, $username);
    			
        }

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