<?php
/**
* This will return the child nodes from the passed page.
*
* Sample returned value:
*       1"Dan Duck"Dan_Duck"false"2"Donald"Donald"true"
* Key ID, Caption, Encoded (linkable) title, has this node's children already been loaded (true or false), ... next record...
*
* @package folio
* @todo Don't include files, find a way to tap directly into the DB w/o having to bring in all of the library stuff.
*       It makes the navigation slower.
* @param int $page The parent page to display.
*/

/*    
            Various properties available to calling code.
		div.innerHTML = "<li>Transaction id: " + o.tId + "</li>";
		div.innerHTML += "<li>HTTP status: " + o.status + "</li>";
		div.innerHTML += "<li>Status code message: " + o.statusText + "</li>";
		div.innerHTML += "<li>HTTP headers: <ul>" + o.getAllResponseHeaders + "</ul></li>";
		div.innerHTML += "<li>Server response: " + o.responseText + "</li>";
		div.innerHTML += "<li>Argument object: Object ( [foo] => " + o.argument.foo +
						 " [bar] => " + o.argument.bar +" )</li>";
    */

    require_once("../../../includes.php");

    $page_ident = required_param('page',0,PARAM_INT);    
    $page = get_record('folio_page','page_ident',$page_ident, 'newest', 1);
    $profile_id = $page->user_ident;
    
	$prefix = $CFG->prefix;
    $run_result = '';
	
    // NOTE: The following query has the potential to return duplicates.  However, the older mysq doesn't support subqueries,  making it impossible
    //  to do the query properly.  Filter out dups in code.
    //      Potential Dup: w.* & children = 0, w.* & children = 1
    //      Filtering on children=1 is a problem, as moving a child of a page, leaves new=0 with the parentpage_ident still set, filtering out that
    //      page.
	$pages = recordset_to_array(
		get_recordset_sql("SELECT DISTINCT w.*, children.newest children FROM {$prefix}folio_page w " .
			"INNER JOIN {$prefix}folio_page_security p ON w.security_ident = p.security_ident " .
			"LEFT OUTER JOIN {$prefix}folio_page children ON w.page_ident = children.parentpage_ident " .
			"WHERE w.parentpage_ident = $page_ident AND w.newest = 1 AND w.parentpage_ident <> w.page_ident AND " . 
			folio_page_security_where( 'p', 'w', 'read', $profile_id ) .
            'ORDER BY title, children DESC')
		);	
        
    $last_ident = -1;
	
	if ( $pages ) {
		foreach ($pages as $page) {
			$i = $page->page_ident;
            
            // Look to see if we're looking at a duplicate.
            if ( $last_ident != $i ) {
               
                // Update last_ident
                $last_ident = $i;

                // Load results
                $run_result .= intval($page->page_ident) . "\"";
                $run_result .= str_replace("\"", "'", $page->title) . "\"";
    			$run_result .= str_replace("\"", "'", folio_page_encodetitle( $page->title ) ) . "\"";

    			if ( is_null ($page->children ) ) {
    				// No kids.  Value is interpreted as 'already loaded', so since no kids, set to already loaded.
    				$run_result .=  "true\"";
    			} else {
    				// Children, set loaded = false
    				$run_result .= "false\"";
    			}			

            } // if ! dup
		} // foreach
	} // if $pages

    

    // Return wrapped up.
    echo $run_result;
    
?>