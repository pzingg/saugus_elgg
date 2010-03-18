<?php
/**
* This is used to post comments via AJAX (called by commentbox.php)
*
* @package folio
*
* @todo Don't include files, find a way to tap directly into the DB w/o having to bring in all of the library stuff.  It makes it slow.
* @todo Generate activity record.
* @param int $item_ident The item being commented upon.
* @param string $item_type The type of item being commented upon.
* @param string $item_title The title of the thing being commented upon.  Note that this is encoded by lib.php's folio_page_encode title.
* @param int $item_owner_ident The id of the user to whom the thing being commented on belongs: NOT THE COMMENTER
* @param string $access The default access level for the comment.
* @param string $body The text of the comment.  Encoded thru javascript.
* @param string $url The url location of the item being commented upon.
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

    
    $comment = new StdClass;

    $comment->item_ident = required_param('item_ident',0,PARAM_INT);
    $comment->item_type = folio_clean( required_param('item_type') );
	$comment->item_title = folio_page_decodetitle( folio_clean( required_param('item_title')));
    $comment->item_owner_ident = required_param('item_owner_ident',0,PARAM_INT);
	$comment->item_owner_name = required_param('item_owner_name');
	$comment->item_owner_username = required_param('item_owner_username');
    $comment->access = folio_clean( required_param('access') );
    $comment->body = required_param('body');
    $comment->posted = time();
	
	$url = required_param('url');
	
	if ( isloggedin() ) {
		$comment->creator_username = $_SESSION['username'];
		$comment->creator_name = $_SESSION['name'];
		$comment->creator_ident = $_SESSION['userid'];
	} else {
		$comment->creator_username = '';
		$comment->creator_name = 'Anonymous';
		$comment->creator_ident = -1;
	}
	
    // Insert new record into db.
    insert_record('folio_comment',$comment);
	
	// Create RSS record
	rss_additem( $comment->item_owner_ident, 
		$comment->item_owner_username, 
		$comment->creator_ident,
		$comment->creator_name,
		$comment->creator_username,
		$comment->item_type . '_comment', 
		$comment->item_ident, 
		'', 
		'Comment by ' . $comment->creator_username . ' on ' . $comment->item_title, 
		$comment->body, 
		$url ) ;
		
    // Return wrapped up.
    echo "<b><font color='#800517'>Comment posted. It will show next time you load the page.</font></b>";

?>