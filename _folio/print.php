<?php
/**
* View a single page
*  Show normal pages.  Note that this file uses rewritten urls set in .htaccess
*
* @package folio
* @param string $user The user/community to which this page belongs.  
* @param string $page The title of the page being shown.
*/    
//	include_once( 'perfstart.php' );
    define("context", "folio_page_view");
    // Run includes
    require("../includes.php");
	require_once("../mod/folio/control/commentlist.php");
	//require_once("../mod/folio/control/breadcrumb.php");
    //require_once("../mod/folio/control/page_view.php");
	require_once("../mod/folio/control/pagelist.php");
	
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
		}
	} else {
		// Transate the page & user vars into an int $page_ident
		$page_ident = folio_page_translatetitle($username, $page_title);
	}
		

	// If we found a matching page, retrieve record & permissions.
	if ($page_ident <> -1) {
		$page = folio_page_select( $page_ident, -1 );
		$permissions = folio_page_security_select( $page_ident );
	} else {
		$page = false;
		$permissions = false;
	}
			

	// Test to see if we have permissions
	$ok = folio_page_permission( $page, $permissions, 'read', $profile_id );

	// If we have permissions to view the page, then continue loading controls.
	//	Also has the side effect of not loading other controls if the page hasnt' been 
	//	created yet.
	if ( $ok ) {
	    // Reset the side menu after defining the comment on variables.
	    $comment_on_type= 'page';
	    $comment_on_ident= $page_ident;
		$comment_on_username = $username;
		$comment_on_name = $name;
		$comment_on_ident = $page_owner;

        if ( !$page ) {
    		// Page does not exist
            
            // Build nice titles
    		$plain_title = 'Create ' . $page_title;
            // Run the command to actually retrieve the content.  
            $page_body = "<p>Page does not exist</p>";
//                folio_page_view($page_ident,$page_title, -1, $page, $username);

        } else {
            // Viewing an existing page.
            
            // Build nice titles
            $plain_title = $page->title;
            
            // Run the command to actually retrieve the content.  
            $page_body = 
            	  folio_page_makelinks( $username, stripslashes( $page->body ) ).
//                folio_print_view($page_ident,$page_title, -1, $page, $username) .
//                folio_control_childpagelist( $username, $page, $page_owner ) .
    			folio_control_commentlist( 'page', $page_ident);
    		
        }
		$created = gmdate("m/d/y h:i",intval($page->created));
        $security_ident = intval($page->security_ident);			
        
        $creator = intval($page->creator_ident);

        if ( $creator < 1 ) {
            $creator = 'Anonymous'; 
        } else {
            $creator = run("users:id_to_name", $creator);
        }
	} else {
    	// We don't have permissions, and so need some sort of title.
    	$plain_title = 'You do not have permission to view this page';
        $page_body = 'You do not have permission to view this page. Please contact the page\'s' .
				' owner and ask for the security to be set to <b>Public</b> or <b>Moderated</b>. ' .
				'  You will be able to view this page once that has been done.  If this page belongs ' .
                ' to a community, you may also try to join the community.  Once you are a member, ' . 
                ' you will be able to see and edit the page.';

    }

    // Transfer into template & write.
    //templates_page_setup();
//    $page_body = templates_draw(array(
//                    'context' => 'contentholder',
//                    'title' => $html_title,
//                    'body' => $page_body
//                )
//                );
    
//    echo templates_page_draw(array(
//                    $plain_title, $page_body
//                )
//                );

    $body .= <<< END
<html>
	<head><title>$plain_title</title></head>
	<body>
		<h3>$plain_title</h3>
        <p>{$page_body}</p><br/><br/>
        <hr>
        <p align=right><i><a href="{$url}$creator">$creator</a>, $created (GMT)</i></p>
	</body>
</html>
END;
		echo $body;
				
//	include_once( 'perfstop.php' );
?>