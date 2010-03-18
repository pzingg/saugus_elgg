<?php
/*
* This function will display a comment submission control for a page.
*
* @todo Include User & perission controls.
* @package folio
* @param string $item_ident Required, the ident number for the thing being comments upon.
* @param int $item_ident Required, the type of thing being commented upon (file, folio, page).
* @param string $item_title Required, the non-encoded title of the thing being commented upon.
* @param string $item_url Required, the url for the thing being commented upon.
* @param int $item_owner_ident Required, the owner of the thing being commented upon.  NOTE: This is NOT the current user (aka, the commenter).
* @param string $item_owner_name Required, the owner of the thing being commented upon.  NOTE: This is NOT the current user (aka, the commenter).
* @param int $item_owner_username Required, the owner of the thing being commented upon.  NOTE: This is NOT the current user (aka, the commenter).
*/
function folio_control_commentbox( $item_ident, $item_type, $item_title, $item_url, $item_owner_ident, $item_owner_name, $item_owner_username ) {
    
    $url= url . 'mod/folio/control/commentbox_postdata.php';
    $ajaxprefix = 'folio_control_commentbox_';
	$item_title = folio_page_encodetitle( $item_title );
	$item_owner_username = folio_page_encodetitle( $item_owner_username );
    
	/* This is now passed as $item_owner_ident
	    if (logged_on) {
	        $userid = $_SESSION['userid'];
	        $comment_name = $_SESSION['name'];
	    } else {
	        $userid = -1;
	        $comment_name = "Guest";
	    }
	*/
    
    $result = <<< END
    <div id="{$ajaxprefix}container"></div>
<script type="text/javascript"> <!--
var {$ajaxprefix}div = document.getElementById('{$ajaxprefix}container');

var {$ajaxprefix}handleSuccess = function(o){
	if(o.responseText !== undefined){
		{$ajaxprefix}div.innerHTML = o.responseText;
        document.{$ajaxprefix}form.{$ajaxprefix}comment.value='';
	}
};

var {$ajaxprefix}handleFailure = function(o){
	if(o.responseText !== undefined){
		{$ajaxprefix}div.innerHTML = o.responseText;
	}
};

var {$ajaxprefix}callback =
{
  success:{$ajaxprefix}handleSuccess,
  failure:{$ajaxprefix}handleFailure,
  argument:['foo','bar']
};

function {$ajaxprefix}makeRequest(){
    var postData = "item_ident={$item_ident}&item_type={$item_type}" +
        "&item_owner_name={$item_owner_name}&item_owner_username={$item_owner_username}&access=public&url={$item_url}&item_owner_ident={$item_owner_ident}&item_title={$item_title}&body=" + 
        escape( document.{$ajaxprefix}form.{$ajaxprefix}comment.value );
	var request = YAHOO.util.Connect.asyncRequest('POST', "{$url}", {$ajaxprefix}callback, postData);
}

-->
</script>
<form name="{$ajaxprefix}form">  
    <p>
    <textarea name="{$ajaxprefix}comment" rows=2 style="
        border: 1px solid #7F9DB9;
        color:#71717B;
        width: 95%;
        padding:3px;" id="{$ajaxprefix}comment" ></textarea>      
    <input type="button" value="Send" onClick="{$ajaxprefix}makeRequest();" />
    </p>
</form>
    
END;
    return $result;
}  
?>