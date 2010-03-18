<?php
/**
* View of list of pages contained by the current page.
* @package folio
**/

/**
* View the history of the page.
* Assumes that the security has already been vetted, aka, the user has permission to view the pagelist.  Will also
*   show past (historical) page links even if the user doesn't have permission to view them.
*
* @uses $CFG
* @param string $username The user/community to whom the record belongs.
* @param array $page The mysql page record.
* @return HTML control listing pages, along with an ((revert)) link.
**/
function folio_control_historypagelist( $username, $page, $profile_id ) {
	global $CFG;
	$url = url;
		
	if (!$page) {
        // No pages passed.  Can't show sub-pages of a page that doesnt' exist.
		return '';
	} else {
		// Grab matching records.

		$pages = recordset_to_array(
		get_recordset_sql(
            "SELECT e.created, u.username creator_username, u.name creator_name, e.title, e.page_ident, e.newest FROM {$CFG->prefix}folio_page e " .
            "LEFT OUTER JOIN {$CFG->prefix}users u on e.creator_ident = u.ident " .
            "WHERE page_ident = {$page->page_ident} ORDER BY created DESC"
            ) );	

		$html = '<p align="center"><a href="' . $url . $username . '/page/' . 
            folio_page_encodetitle( $page->title ) . '"><b>Go to current version</b></a></p>';

	    if ( $pages ) {
			// Build html
			$html .= '<ul>';
	        foreach ($pages as $historypage ) {
            
                // Address
                if ( $historypage->newest == 1 ) {
                    $http = "$url{$username}/page/" . folio_page_encodetitle( $page->title );
                } else {
                    $http = "$url{$username}/page/{$historypage->page_ident}:{$historypage->created}";
                } 
                
	            // User.  
                if ( is_null( $historypage->creator_name ) ) {
                    $by = ' by Anonymous';
                } else {
                    $by = ' by ' . $historypage->creator_name;
                }
                
	            $html .= "<li><a href='{$http}'>" .
					 gmdate("m/d/y h:i",intval($historypage->created)) .' :: ' . 
                     $historypage->title . "</a>" . $by;
                     
	            
	        }
			$html .= '</ul>';
		} 

	}    

    return $html;
}

function folio_control_childpagelist( $username, $page, $profile_id ) {
	global $CFG;
	$url = url;
		
	if (!$page) {
        // No pages passed.  Can't show sub-pages of a page that doesnt' exist.
		return '';
	} else {
		// Grab matching records.

		$pages = recordset_to_array(
		get_recordset_sql("SELECT DISTINCT w.* FROM " . $CFG->prefix . "folio_page w " .
			"INNER JOIN " . $CFG->prefix . "folio_page_security p ON w.security_ident = p.security_ident " .
			'WHERE w.parentpage_ident = ' . $page->page_ident . ' AND w.page_ident <> ' . $page->page_ident .
			' AND w.newest = 1 AND ' . 
			folio_page_security_where( 'p', 'w', 'read', $profile_id ) . ' ORDER BY title ')
		);	

		$html = '<a href="' . $url . $username . '/page/' . 
			folio_page_encodetitle( $page->title ) . '/addpage">Add a new page under this one</a><br/>';
	    if ( $pages ) {
			// Build html
			$html .= '<ul>';
	        foreach ($pages as $childpage ) {
	            // Load values.  
	            $html .= "<li><a href=\"$url" . $username . '/page/' . 
					folio_page_encodetitle($childpage->title) . '">' .
					$childpage->title . "</a>\n";
	            
	        }
			$html .= '</ul>';
		} 

	}    

    return $html;
}
  
?>