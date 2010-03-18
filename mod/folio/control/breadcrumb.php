<?php
/**
* This function will display a breadcrumb control for a page.
* It is formatted to display the page title as a breadcrumb control, aka, the title of the current page will always be shown.
*
* EXAMPLE OUTPUT
*       <a><u>Page1</a><u> > <a><u>Page2</a></u> > ... > CurrentPage<br/>
* @package folio
* @todo Include User & perission controls.
* @param $page_ident Required, the root node being shown.
* @param $parentpage_ident Required, the parent ident of the current node.
* @param $title Required, the name of the root node being shown.
*/

// Recursively find the parent information.  Called by folio_control_breadcrumb.
function folio_control_breadcrumb_recursive_find($page_ident, $url, $username) {

    //   Find the root page information.
    $page = folio_page_select( $page_ident );

    if (!$page) {
        // No results returned.  Silent fail, as this could be a new record.
		return '';
    } else {
		// Test for exit condition
		
		if ( $page->parentpage_ident == -1 || $page->parentpage_ident == $page->page_ident ) {
			// Exit, we've reach the homepage.

			return "<a href='{$url}{$username}/page/" . folio_page_encodetitle( $page->title )
				. "'>" . stripslashes($page->title) . "</a> &gt; ";
		} else {
			// Recurse.
			return folio_control_breadcrumb_recursive_find($page->parentpage_ident, $url, $username) . 
				"<a href='{$url}{$username}/page/" . folio_page_encodetitle( $page->title )
				. "'>" . stripslashes($page->title) . "</a> &gt; ";
		}
		
    }        
}

function folio_control_breadcrumb( $page, $username ) {
    //   Find the root page information.
    $url = url;
    			
    // Test to see if this is a new page (hense, $page is false)
    if ( !$page ) {
        return '';
    } 

    // Begin recursion.
    // The homepage for each user is signified by having a page_ident = parentpage_ident.
    if ( $page->parentpage_ident <> $page->page_ident ) {
        $run_result = folio_control_breadcrumb_recursive_find( $page->parentpage_ident, $url, $username) ; 
    } else {
        $run_result = '';
    }
    
    return trim($run_result . $page->title);
}

?>