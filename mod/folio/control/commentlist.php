<?php
/*
* View of list of comments made for the passed object
*
* @package folio
* @todo Implement results & offset, currently ignored.
* @todo IMplement security.
* @param $item_type corresponds to the type of thing being commented upon. value:    file, folio or *,  page
* @param int $item_ident The type of thing to be commented upon set by $item_type
* @param int $results (def = 10)
* @param int $offset ( def = 0)
*/
function folio_control_commentlist( $item_type, $item_ident, $results=10, $offset=0 ) {

    // Validate type.
    if (!($item_type == 'file' || $item_type == 'rfolio' || $item_type == 'page' || $item_type == '*')) {
        error('Invalid item_type passed to rfolio_comment_view_list');
        die();
    }
    
    $run_result = '';
    $url = url;
    // Set number of returned records.
    //$results = "limit $offset,$results ";

    // TODO: Implement a way of gather in (recursively) contained objects for a folio.
    //if ($item_type == '*' || $item_type == 'rfolio') {
        // Select all matching rfolio value.
    //    $comments = (get_records_select('folio_comment', 
    //        "item_ident = $item_ident", 
    //        null, 'posted desc', '*'));
    //} else {

    // Only return the specific type.
    $comments = (get_records_select('folio_comment', 
        "item_ident = $item_ident AND item_type = '$item_type'", 
        null, 'posted desc', '*')); 
	$html = '';

    if ( $comments ) {
        foreach ($comments as $comment) {
            // Load values.  
            $body = htmlspecialchars(stripslashes($comment->body));
			$postedbylink = "<a href='{$url}{$comment->creator_username}'>{$comment->creator_name}</a>";
            $posted = gmdate("m/d/y H:i",intval($comment->posted));
            
            $html .= <<< END
                <div class="infoholder">
                <p style="border: 1px solid #7F9DB9; width: 95%; padding:3px;"> 
                {$body}<br/>
                <span style="color:#71717B; ">&nbsp;&nbsp;&nbsp;<i>{$postedbylink} - {$posted} (GMT)</i></span></p>
                </div>
END;
            
        }
    } 
    return $html;
}
  
?>