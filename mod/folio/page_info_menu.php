<?php
/**
* Display a sidebar for a page.
* @package folio
**/
    // Depends upon the following globals being set.
    global $comment_on_type;
    global $comment_on_ident;
    global $page_ident;
    global $comment_on_username;
	global $comment_on_name;
	global $comment_on_ident;
	global $page_title;

//    require_once("../mod/folio/control/commentbox.php");
//    $page_info_menu_body = "<li><h2>Publish Comment:</h2>" .
//        folio_control_commentbox($page_ident, 'page', $page_title, url . $comment_on_username . '/page/' . folio_page_encodetitle( $page_title ),
//		$comment_on_ident, $comment_on_name, $comment_on_username );   
    require_once("../mod/folio/control/tree.php");
    $page_info_menu_body .= "<li id=\"sidebar_pages\"><h2>Wiki Pages:</h2>" .
        folio_control_tree($page_ident, $page_title, $comment_on_username) . "</li>";   

    $run_result .= $page_info_menu_body
	
	/*.= templates_draw(array(
                    'context' => 'sidebarholder',
                    'title' => '',
                    'body' => $page_info_menu_body
                )
                );
    */
?>