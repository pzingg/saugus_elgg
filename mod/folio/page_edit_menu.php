<?php
/**
* Display a sidebar for a page.
*
* @package folio
**/

    // Depends upon the following globals being set.
    global $comment_on_type;
    global $comment_on_ident;
    global $rfolio_ident;
    global $page_ident;
	global $username;
    
	/*
    require_once("../mod/folio/control/tree.php");
	

    $page_edit_menu_body = "<li><h2>Insert Picture:</h2>" .
        folio_control_tree($page_ident, "Pictures", $username);   

    $page_edit_menu_body  .= <<< END
    <form><input type=file /><input type="button" value="Insert Image" onClick="">
    </form>
    <br/>
    <INPUT TYPE=CHECKBOX NAME="publishinblog">Publish in Blog<br/>
    <INPUT TYPE=CHECKBOX NAME="publishinprofile">Publish in my Profile<br/>
	<br/>
	Note, none of the stuff above is actually implemented yet. The tree menu will show a list of
	all images the user has uploaded.  Users can drag n drop images into their pages, or use some
	sort of [insert image] button.  Also have another section for files, so that they can easily 
	link to a file.  Lastly, have a section for pages so that they can see what else is out there
	and remember the types of links.
END;
            
			
    $run_result .= templates_draw(array(
                    'context' => 'sidebarholder',
                    'title' => 'Settings',
                    'body' => $page_edit_menu_body 
                )
                );
    */
?>