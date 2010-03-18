<?php

/**
 * View a single wiki page.  If the passed key is -1, then it will assume that we're pulling up a new page.
 * 
 * @uses CFG
 * @param int $page_ident The page being viewed. -1 If we're trying to load a non-existing page.
 * @param string $page_title The title of the page.  Important for when we're pulling up a page that hasn't been created yet.
 * @param int $created The created date for the page (optional, only used to pull up history pages, def = -1)
 * @param array $page The mysql page record.
  * @returns The html stuff to display a page.
 **/
function folio_page_view($page_ident, $page_title, $created =-1, $page, $username) {
    global $CFG;
    //global $profile_id;
    //global $page_owner;
    $url = $CFG->wwwroot;
	$userid = $_SESSION['userid'];
	
    // Declare variables.
    $body = ''; $title = 'Title'; $parentpage_ident = -1;

	if (!$page) {
        // No results returned.  Offer to create a new page.
		if ($page_title == 'Home Page') {
			$page_body = "You can use this part of Elgg as a wiki, to collaboratively work on projects, " . 
			"or to create an online portfolio.\n\nYou can create links to new pages by simply " .
			"enclosing the page title using double bracket characters.  Here's an example " .
			"of a link to your <a href=\"{$url}{$username}/page/" . 
				folio_page_encodetitle($page_title) . 
				"/edit\">[[Home Page]]</a>.";
		} else {
			$page_body = "This page has not yet been created. <a href=\"{$url}{$username}/page/" . 
				folio_page_encodetitle($page_title) . 
				"/edit\">Click here to start editing it</a>.";
		}
	    $page_title = '';
	    $creator = '';
	    $created = '';
		$security_ident = $page_ident;

    } else {
		// Load values.

        $page_title = stripslashes($page->title);
        $page_body = folio_page_makelinks( $username, stripslashes( $page->body ) );
        $created = gmdate("m/d/y h:i",intval($page->created));
        $security_ident = intval($page->security_ident);			
        
        $creator = intval($page->creator_ident);

        if ( $creator < 1 ) {
            $creator = 'Anonymous'; 
        } else {
            $creator = run("users:id_to_name", $creator);
        }
        
		

    }    

    // Building the html control.
    $body .= <<< END
        <p>{$page_body}</p><br/><br/>
		<h1></h1>
        <p align=right><i>$creator, $created (GMT)</i></p>
END;

    return $body;

}
?>