<?php
/**
* Functions used to browse pages, files, blogs, & RSS stuff.
*
* @package folio
**/

// Used by the different php functions in this file to prefix all of the ajax stuff being output to the screen.
//	May eventually be used to allow for including a number of instances of this control on a single page.
//$ajaxprefix = 'folio_control_browse_';

/**
* Return a navigation tree local to the passed page.
*
* @param int $page_ident The page for which we're going to create the tree for.
* @param string $title The passed page's title.  Used to avoid having to run another db call.
**/
/*
function folio_control_browse( $page_ident, $title ) {
    // This function will return a working ajax navigation tree.
	//	Note: Do not change the name of the root node to anything but root without also changing it in the helper functions in this file.
    global $CFG;
    global $metatags;
    global $ajaxprefix;
    $address = $CFG->wwwroot . 'mod/folio/';

    // Add CSS link.
    $metatags .= "\n<link rel=\"stylesheet\" type=\"text/css\" " . 
        "href=\"{$address}css/treeview.css.php\">";
    
    // Build javascript to load parent information.
    $buildNodes = folio_control_browse_getNodeParent( $page_ident ) 
		. folio_control_browse_getNodeChildren( $page_ident);
    
    // BUILD JAVASCRIPT
    $body .= <<< END

        <script type="text/javascript" src="{$address}control/yui/build/yahoo/YAHOO-min.js" ></script>
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
        var id = -1;
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
                if (id != "-1") {
                    {$ajaxprefix}buildNode(id, responseText.substring(0, i) , {$ajaxprefix}g_loadDataNode, false) ;
                    id = "-1" ;
                } else {
                    id = responseText.substring(0, i) ;
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
function {$ajaxprefix}buildNode(id, caption, parentNode, loaded) {
    var myobj = { label: caption + " <a href='{$address}../../_folio/view.php?page=" 
        + id + "'>(open)</a>", id: id } ;
    if (loaded) {
        var tmpNode = new YAHOO.widget.TextNode(myobj, parentNode, true);
    } else {
        var tmpNode = new YAHOO.widget.TextNode(myobj, parentNode, false);
        tmpNode.setDynamicLoad({$ajaxprefix}loadDataForNode); 
    }
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
<div class="infoholder" style="border: 1px solid #7F9DB9; 
    padding:3px;margin-left: 0.5em;">
    <b>Pages</b> <a href="{$url}edit.php?parentpage=$page_ident&new=Y">(add)</a>
      <div id="{$ajaxprefix}treeDiv1"></div>
</div>
<img src="{$address}control/tree_blankimage.jpg" onload="javascript:{$ajaxprefix}treeInit()"  />

END;
    
    // return    
    return $body;
*/
/*
* In case I need the code for the 'contract all', here it is
      	  <div id="expandcontractdiv" onload="javascript:treeInit()">
    		<a href="javascript:tree.collapseAll()">Collapse all</a>
    	  </div>
*/  

/*
}
*/

/**
* Recursively retrieve parent of the passed node & return javascript cdoe used to build nodes.
* Note: Calling pages should not set $parentpage_ident.  The first called record needs this to be set to -1.  This
*	is done so that the code knows to bold the selected page.
*
* @param int $page_ident The page currently being retrieved.
* @param string $result
* @param int $parentpage_ident Used for recursion, do not set directly.
* @return string HTML code used for building nodes.
*/
/*
function folio_control_browse_getNodeParent($page_ident, $result = '', $parentpage_ident = '-1') {
	global $ajaxprefix;
	global $CFG;
	
	$pages = recordset_to_array(
		get_recordset_sql("SELECT w.* FROM " . $CFG->prefix . "folio_page w " .
			"INNER JOIN " . $CFG->prefix . "folio_page_security p ON w.security_ident = p.security_ident " .
			"WHERE w.page_ident = $page_ident AND w.newest = 1 AND " . 
			folio_page_security_where( 'p', 'read' ) . ' LIMIT 1 ')
		);			
			
	if ( $pages ) {
		foreach ($pages as $page) {
			// Load values.  Remove " from titles & replace with ', and " are used as delimiters by tree.php's javascript AJAX function.
			
			// Get info.
			$nextI = intval($page->parentpage_ident);

			// Replace the parent node's child with this node.
			$result = str_replace("%" . $parentpage_ident . "root%", "{$ajaxprefix}Node{$page_ident}", $result);

			// Build title.
			if ( $parentpage_ident == '-1' ) {
				// This is the current node, apply highlighting.
				$page_title = "<b>" . str_replace("\"", "'", stripslashes($page->title)) . "</b>";
			} else {
				$page_title = str_replace("\"", "'", stripslashes($page->title));
			}
			
			$result = "var {$ajaxprefix}Node{$page_ident} = {$ajaxprefix}buildNode( \"$page_ident\", \"" .
				 $page_title . 
				"\", %{$page_ident}root%, true);\n" .
				$result;

			if ( $nextI == -1 ) {
				// This is the root node.
				$result = str_replace("%{$page_ident}root%", "root", $result);                
			} else {
				// More nodes, recurse.
				$result = folio_control_browse_getNodeParent( $nextI, $result, $page_ident ) ;
			}
		}
	} else {
		// We didn't find this node, which was to have been the parent of the previously-called recursive node.
		//	This probably occurred because the user hasn't given current user permission to access the root page.
		//	Therefore, we need to replace the placeholder node with the root node.
		$result = str_replace("%" . $parentpage_ident . "root%", "root", $result);

	}
	
	return $result;
}
*/
/**
* Builds the passed node's child nodes
* Depends upon the root node being named 'root' in the other functions in this file.
*
* @param int $page_ident The page for which we want the children.
* @return HTML code for building the child nodes.
**/
/*
function folio_control_browse_getNodeChildren($page_ident) {

	global $ajaxprefix;
	global $CFG;
	

		$pages = recordset_to_array(
		get_recordset_sql("SELECT DISTINCT w.* FROM " . $CFG->prefix . "folio_page w " .
			"INNER JOIN " . $CFG->prefix . "folio_page_security p ON w.security_ident = p.security_ident " .
			"WHERE w.parentpage_ident = $page_ident AND w.newest = 1 AND " . 
			folio_page_security_where( 'p', 'read' ) . ' ORDER BY title ')
		);	

		
		
	// Set the parent node's name.
	if ( $page_ident == -1 ) {
		$parentnode = 'root';
	} else {
		$parentnode = 'Node' . $page_ident;
	}

	if ( $pages ) {
		foreach ($pages as $page) {
			// Load values.  Remove " from titles & replace with ', as " are used as delimiters by tree.php's javascript AJAX function.
			$i = $page->page_ident;
			
			$result = "var Node{$i} = " . $ajaxprefix . 
				"buildNode( \"$i\", \"" .
				str_replace("\"", "'", stripslashes($page->title)) . 
				"\", {$parentnode}, false);\n" .
				$result;
		}
	} 
	
	return $result;
}
*/
?>