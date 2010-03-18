<?php
/**
* Delete a wiki page.
*
* @package folio
**/

/**
* Delete a single wiki page.  Assumes that permission check has already been run.
*       Looks to see if there are any child pages, if so, doesn't allow deleting until those are
*       removed as well.
*
* @package folio
* @param array $page The mysql page record.
* @param string $page_title The passed in title used to access the page.  Assumes that it has already 
*	been decoded by the function in lib.php away from the URL form & into the normal presentation form.
* @param string $username The username of the page owner.  Used to create post link
* 	to the finished page.
* @returns HTML code to delete a folio page.
**/
function folio_page_delete($page, $page_title, $username) {
    global $CFG;
    global $profile_id;
    global $language;
    global $page_owner;
	global $metatags;
    
    // Set url var
	$url = url;

    // Error, need a page record.
    if (!$page ) {
		error('Sorry, but you can not delete a page that has not yet been created.');
    } 
	
    // Get children records.
    $pages = recordset_to_array(
        get_recordset_sql('SELECT * FROM ' . $CFG->prefix . 'folio_page p ' .
            "WHERE parentpage_ident = {$page->page_ident} AND newest = 1")
        );

    // Build results
	if ( $pages ) {
        // don't offer to delete pages with children.  link to titles
        $run_result = 'Sorry, but you can not delete a page that has child pages under it.  Delete each' .
            ' of the child pages, and then come back and delete this page.<br/>' .
            '<ul>';
            
		foreach ($pages as $page) {
			$run_result .= "<li><a href=\"{$url}{$username}/page/" . folio_page_encodetitle($page->title) . "\">{$page->title}</a>";
		}
        $run_result .= "</ul>";
	} else {
    
        $run_result = <<< END
            
        <form method="post" name="elggform" action="{$url}_folio/action_redirection.php">
        	<h2>{$page_title}</h2>
        	<p>
                Click the 'delete' button to completely remove this page.  You will not be able to undo this process.<br/>
        		<input type="hidden" name="action" value="folio:page:delete" />
        		<input type="hidden" name="page_ident" value="{$page->page_ident}" />
        		<input type="submit" value="Delete" />
        	</p>
END;
    }
    
    return $run_result;
}
        
?>