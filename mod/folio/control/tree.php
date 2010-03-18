<?php
/**
* Used to show a tree of pages & files
* @package folio
**/

/**
* Recursively build the nodes for the tree control.
*
* Creates the following entries.
* 	var folio_control_tree_Node711395263 = folio_control_tree_buildNode( "711395263", "Donald", folio_control_tree_Node525451660, false);
*	Note that the 2nd to last argument in the above is the new node's *parent* node.  
*
* This is written with terminology of up being towards the root, down being towards the leaf nodes.
* 	We're originally passed the starting node. 
*	Build the starting node node.
*	While the current node is not the root node.
*		Find the node's parent (go up)  information.
*		Build the parent node.
*		Go back to the previous (down) node & set that node as a child of the parent (up) node.
*		Get current node's other child nodes, passing the previously created (down) node 
			as an argument so that it won't be built 2x.
*	repeat until we hit this trees base node (where parentpage_ident == page_ident).
*
* NOTE: Calling functions SHOULD NOT SET $previouspage_ident.  
* NOTE: Depends upon the root node being named 'root'.  This is the root node of the tree, not the root
*	'Home Page' page.
*
* @param $page_ident int The page currently being shown, whose node we'll build the rest of the tree around.
* @param $result string The html code assembled so far.  Only the last recusion returns anything, as it
*	has to modify html put together by previous recursions.
* @param $previouspage_ident int Optional.  Used to set the owner of previously created nodes.  AKA, for Nodes a->b->c,
*	c is called first, then b.  However, c's node needs to be reset *after* b's is created.  This variable keeps track
*	of this reverse-resetting.  It should not be set for the first iteration, defaulting to -1.
* @return string The HTML code of Nodes to be used by folio_control_tree
**/
function folio_control_tree_getNodes( $ajaxprefix, $page_ident, $result = '', $previouspage_ident = -1 ) {
	global $CFG;
	global $username;
    global $profile_id;
    
	$url = url . $username . '/page/';
	$prefix = $CFG->prefix;

	// Get the root page.
    // Note: This SQL Query was a major pain to get working, as older versions of apache don't support subqueries (which is the logical
    //  way to write this).  As a result, I have a left outer join, order by, and a limit to try and get the results to show up properly in all 
    //  circumstances.  Before changing, be sure that all of these cases are supported by the revised logic:
    //      Private page, with a public child page under it, then move the public page to another parent and see if the private page
    //          is still seen.


	$pages = recordset_to_array(
		get_recordset_sql("SELECT w.*, children.newest children FROM {$prefix}folio_page w " .
			"INNER JOIN {$prefix}folio_page_security p " .
			"ON w.security_ident = p.security_ident " .
			"LEFT OUTER JOIN {$prefix}folio_page children " .
			"ON w.page_ident = children.parentpage_ident " .
			"WHERE w.page_ident = $page_ident AND w.newest = 1 AND " . 
			folio_page_security_where( 'p', 'w', 'read', $profile_id ) .
            'order by children DESC LIMIT 1')
		);			
		
	if ( $pages ) {
		$page = $pages[$page_ident];
		// Replace " with ', as the asynch loading mechanism does the same.
		$page_title = str_replace("\"", "'", stripslashes($page->title));
		
		// Create children nodes (if they exist).  Don't create a node for previouspage_ident record, 
		//	as it has already been created in the previous recursion.
		if ( $page->children == 1 ) {
			$result = folio_control_tree_getNodeChildren( $ajaxprefix, $page_ident, $previouspage_ident, $url, $result, $profile_id);
			$result .= "\n{$ajaxprefix}Node{$page_ident}.expand();\n";
		}
		
		// Replace the parent node's child_of_node setting with this node.
		if ($previouspage_ident != -1 ) {
			$result = str_replace("%" . $previouspage_ident . "root%", "{$ajaxprefix}Node{$page_ident}", $result);
		}

		// Build html NODE for the passed page_ident.
		$result = "    var {$ajaxprefix}Node{$page_ident} = {$ajaxprefix}buildNode( \"$page_ident\", \""
			 . $page_title . "\", %{$page_ident}root%, true, \"" . $url . folio_page_encodetitle($page->title) . "\");\n" . $result;

		if ( $page->parentpage_ident == $page->page_ident ) {
			// This is the root node of the tree. Remove its root node with the root node of the tree. Don't recurse.
			// Add code to automatically expand.
			$result = str_replace("%{$page_ident}root%", "root", $result);      
			$result .= "\n{$ajaxprefix}Node{$page_ident}.expand();\n";

		} else {
			// There should be more nodes, recurse.
			$result = folio_control_tree_getNodes( 
				$ajaxprefix, $page->parentpage_ident, $result, $page->page_ident 
				);
		}
	} else {
		// We didn't find this node, which was to have been the parent of the previously-called recursive node.
		//	This probably occurred because the user hasn't given current user permission to access the root page.
		//	Therefore, we need to replace the placeholder node with the root node.
		$result = str_replace("%" . $previouspage_ident . "root%", "root", $result);

	}
	
	return $result;
}

/**
* Builds the child nodes for the passed page_ident.
*
* @param int $page_ident The parent for whom we're retrieving children.
* @param int $ignorepage_ident Optional. A page to leave out of the results (if found).  Used by getNodeParents.
**/
function folio_control_tree_getNodeChildren( $ajaxprefix, $page_ident, $ignorepage_ident,$url, $previousresults, $profile_id) {
	global $CFG;
	$prefix = $CFG->prefix;

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
            'ORDER BY title DESC, children DESC')
		);	

        
	// Set the parent node's name.
	$parentnode = $ajaxprefix . 'Node' . $page_ident;
    $last_ident = -1;
	$result = '';
	
	if ( $pages ) {
		foreach ($pages as $page) {
			$i = $page->page_ident;
            
            // Look to see if we're looking at a duplicate.
            if ( $last_ident != $i ) {
               
                // Update last_ident
                $last_ident = $i;
			
    			// Test to see if we're on the *ignore* page, in which case, insert the nodes that have been built thus far.
    			if ( $ignorepage_ident == $i ) {
    				$result = $previousresults . "\n" . $result;
    			} else {

    				if ( is_null( $page->children ) ) {
    					// No kids
    					$result = "     var {$ajaxprefix}Node{$i} = {$ajaxprefix}buildNode( \"$i\", \"" .
    						str_replace("\"", "'", $page->title) . 
    						"\", {$parentnode}, true, \"" . $url . folio_page_encodetitle($page->title) . "\");\n" .
    						$result;
    				} else {
    					// Children, set loaded = false
    					$result = "     var {$ajaxprefix}Node{$i} = {$ajaxprefix}buildNode( \"$i\", \"" .
    						str_replace("\"", "'", $page->title) . 
    						"\", {$parentnode}, false, \"" . $url . folio_page_encodetitle($page->title) . "\");\n" .
    						$result;
    				}
    			}
            } else { // $last_ident == $i
                // Don't load, as it would be a duplicate record.
            }
		} // foreach
	} // if $pages
	
	return $result;
}

/**
* Public code that should be called to create a tree.
*
* @param $page_ident Required, the root node being shown.
* @param $page_title Required, the name of the root node being shown.
* @param $username The name of the user/community to whom this page belongs.
*/
function folio_control_tree( $page_ident, $page_title, $username ) {
	//	Note: Do not change the name of the root node to anything but root without also changing it in the helper functions in this file.
    global $CFG;
    global $metatags;
    global $ajaxprefix;
    $address = $CFG->wwwroot . 'mod/folio/';

	// Used by the different php functions in this file to prefix all of the ajax stuff being output to the screen.
	//	May eventually be used to allow for including a number of instances of this control on a single page.
	$ajaxprefix = 'folio_control_tree_';
	
    // Add CSS link.
    $metatags .= "\n<link rel=\"stylesheet\" type=\"text/css\" " . 
        "href=\"{$address}css/treeview.css.php\" />";
    
    // Build javascript to load parent information.
    $buildNodes = folio_control_tree_getNodes( $ajaxprefix, $page_ident );
    
    // BUILD JAVASCRIPT
    $body = <<< END

        <script type="text/javascript" src="{$address}control/yui/build/yahoo/yahoo-min.js" ></script>
        <script type="text/javascript" src="{$address}control/yui/build/treeview/treeview-min.js" ></script>
        <script type="text/javascript" src="{$address}control/yui/build/connection/connection-min.js" ></script>

        <script type="text/javascript">
    <!--
var {$ajaxprefix}tree;
var {$ajaxprefix}g_loadDataNode;
var {$ajaxprefix}g_onCompleteCallback;

function {$ajaxprefix}treeInit() {
       {$ajaxprefix}tree = new YAHOO.widget.TreeView("{$ajaxprefix}treeDiv1");
       var root = {$ajaxprefix}tree.getRoot();
       
       // Add Nodes
       $buildNodes
              
       {$ajaxprefix}tree.draw();
}

// Callback that loads nodes.  Relies upon global java var loadDataNode
var {$ajaxprefix}handleSuccess = function(o){
    // Add sub-nodes to the collection of tree nodes.
    var id= {$ajaxprefix}g_loadDataNode.data.id;

	if(o.responseText !== undefined){
        // Load
		var linkablecaption = '';
		var caption = '';
        var column = 1;
        var i = -1;
        var responseText = o.responseText;

        while (responseText.length > 0) {
            i = responseText.indexOf('"');
            
            if (i < 0) {
                // Reached end.
                responseText= "";
            } else {
                // Found

                // Test to see if we need to store the value & build the node.
                if (column == 4) {
                    {$ajaxprefix}buildNode(id, caption, {$ajaxprefix}g_loadDataNode, responseText.substring(0, i), linkablecaption) ;
                    column = 1;
                } else if (column == 3) {
                    linkablecaption = responseText.substring(0, i);
                    column = 4;
                } else if (column == 2) {
                    caption = responseText.substring(0, i);
                    column = 3;
                } else {
                    id = responseText.substring(0, i) ;
					column = 2;
                }
                responseText = responseText.substr(i + 1);
            }
        }
    }
    // Data load is complete
    {$ajaxprefix}g_onCompleteCallback ();

}

var {$ajaxprefix}handleFailure = function(o){
    // Silent fail
}

var {$ajaxprefix}callback =
{
  success:{$ajaxprefix}handleSuccess,
  failure:{$ajaxprefix}handleFailure,
  argument: { foo:"foo", bar:"bar" }
};


// Build the node off of the passed data, return completed node.
// If loaded = true, then don't set dynamic load & expand.
function {$ajaxprefix}buildNode(id, caption, parentNode, loaded, link) {
    var myobj = { label: caption, id: id } ;
	// Need the following == 'false' hack, as javascript seems to sometimes interpret the ajax load
	//	as a string rather than a boolean.
    if (!loaded || loaded == 'false') {
        var tmpNode = new YAHOO.widget.TextNode(myobj, parentNode, false);
        tmpNode.setDynamicLoad({$ajaxprefix}loadDataForNode); 
    } else {
        var tmpNode = new YAHOO.widget.TextNode(myobj, parentNode, false);
    }
	// Build html link.
	tmpNode.href=link;
    return tmpNode  ;
}

// Function called to dynamically load data.
function {$ajaxprefix}loadDataForNode(node, onCompleteCallback) {
    {$ajaxprefix}g_loadDataNode = node;
    {$ajaxprefix}g_onCompleteCallback = onCompleteCallback;
        
	var request = YAHOO.util.Connect.asyncRequest('GET', 
        "{$address}control/tree_getdata.php?page=" + {$ajaxprefix}g_loadDataNode.data.id, 
        {$ajaxprefix}callback);
}
-->
</script>
<!-- width: 40%; float: right;  -->
	<ul>
      <li id="{$ajaxprefix}treeDiv1">
		<img src="{$address}control/tree_blankimage.jpg" onload="javascript:{$ajaxprefix}treeInit()"  />
	  </li>
    </ul>
END;
    
    // return    
    return $body;

/*
* In case I need the code for the 'contract all', here it is
      	  <div id="expandcontractdiv" onload="javascript:treeInit()">
    		<a href="javascript:tree.collapseAll()">Collapse all</a>
    	  </div>
*/  
}

?>