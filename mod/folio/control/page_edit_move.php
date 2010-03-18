<?php
/**
* This function will display a  control to move a page
* Warning: Do not change values for field names w/o also updating the save procedure.
*
* @todo Add a recursive check to make sure that this page isn't having its parentpage to set a child page.  At the moment,
*	the sql query prevents it from being set to a direct descendent.  However, a child's child would still be shown in the list,
*	and therefore be a valid entry (according to the program).
* @package folio
* @param $page Required, the page being shown.
*/

/*
* Build the html code for a security box.
*
* @returns HTML control information.  
*/
function folio_control_page_edit_move( $page ) {
    // Find the security information for the page.
	global $CFG;
    $url = url;
	$page_ident = intval($page->page_ident);
	$parentpage_ident = intval($page->parentpage_ident);
	

	// Check to see if we're on the homepage.
	if ( folio_page_is_homepage( $page ) ) {
		// Don't allow moving a homepage.
	
		$run_result = '<input type="hidden" name="parentpage_ident" value="' . $page->parentpage_ident . '" />';
							
	} elseif ( !isloggedin() ) {
		// Have to be logged in to move a page.
		// mark control as disabled & don't bother loading all of the pages.
		$run_result = "			<SELECT NAME=\"parentpage_ident\" DISABLED>";
		
		// Get parentpage title 
	    $pages = recordset_to_array(
			get_recordset_sql('select page_ident, title from ' . $CFG->prefix . 'folio_page ' .
				'WHERE newest = 1 and user_ident = ' . $page->user_ident . 
				' AND page_ident = ' . $page->parentpage_ident)
			);
		
		// build
		if ( $pages ) {
			// Iterate
			foreach ($pages as $potentialpage) {
					// Selected
					$run_result .= '<OPTION VALUE=' . $potentialpage->page_ident 
						. " SELECTED=true>" . $potentialpage->title . "\n";
				}
			$run_result .= "</SELECT><br/>\n"
                . "<input type='hidden' name='parentpage_ident' value='{$potentialpage->page_ident}' />\n";
			
		} else {
			// No pages.  Show control set to homepage & disabled.
			$run_result = "			<SELECT NAME=\"parentpage_ident\" disabled=TRUE>"
				. '<OPTION VALUE="' . $page->parentpage_ident . '" SELECTED=true>Homepage' 
				. "</SELECT><br/>\n"
                . "<input type='hidden' name='parentpage_ident' value='{$potentialpage->page_ident}' />\n";
		}
		
		$run_result = templates_draw(array(
                                'context' => 'databoxvertical',
                                'name' => 'Parent Page',
                                'contents' => $run_result
                            )
                            );
							
	} else {
		// Ok conditions, build the control.
		$run_result = "			<SELECT NAME=\"parentpage_ident\">";
		
		// Get all titles for active pages belonging to the current user
	    $pages = recordset_to_array(
			get_recordset_sql('select page_ident, title from ' . $CFG->prefix . 'folio_page ' .
				'WHERE newest = 1 and user_ident = ' . $page->user_ident . 
				' AND page_ident <> ' . $page->page_ident . 
				' AND parentpage_ident <> ' . $page->page_ident . 
				' order by title')
			);
		
		// build
		if ( $pages ) {
			// Iterate
			foreach ($pages as $potentialpage) {
				if ($page->parentpage_ident == $potentialpage->page_ident ) {
					// Selected
					$run_result .= '<OPTION VALUE=' . $potentialpage->page_ident 
						. " SELECTED=true>" . $potentialpage->title . "\n";
				} else {
					// !Selected
					$run_result .= '<OPTION VALUE=' . $potentialpage->page_ident 
						. " >" . $potentialpage->title . "\n";				
				}
			}
			
			$run_result .= "</SELECT><br/>\n";
			
		} else {
			// No pages.  Show control set to homepage & disabled.
			$run_result = "			<SELECT NAME=\"parentpage_ident\" disabled=TRUE>"
				. '<OPTION VALUE="' . $page->parentpage_ident . '" SELECTED=true>Homepage' 
				. "</SELECT><br/>\n";
		}
		
		$run_result = templates_draw(array(
                                'context' => 'databoxvertical',
                                'name' => 'Parent Page',
                                'contents' => $run_result
                            )
                            );

	}
	
	
	return $run_result;
}


?>