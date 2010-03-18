<?php

    global $page_owner;

    if (logged_on) {
    
    
    if (!run("permissions:check", "weblog")) {
        if (logged_on) {
            $page_owner = $_SESSION['userid'];
        } else {
            $page_owner = -1;
        }
    }
    
    $contentTitle = trim(optional_param('title'));
    $contentBody = trim(optional_param('body'));
    
    $redirect = url . run("users:id_to_name", $page_owner) . "/weblog/";
    
    $username = $_SESSION['username'];
    $addPost = gettext("Add a new post"); // gettext variable
    $postTitle = gettext("Post title:"); // gettext variable
    $postBody = gettext("Post body:"); // gettext variable
    $Keywords = gettext("Keywords (Separated by commas):"); // gettext variable
    $keywordDesc = gettext("Keywords commonly referred to as 'Tags' are words that represent the weblog post you have just made. This will make it easier for others to search and find your posting."); // gettext variable
    $accessRes = gettext("Access restrictions:"); // gettext variable
    $postButton = gettext("Post"); // gettext variable
    
    $body = <<< END

<form method="post" name="elggform" action="$redirect" onsubmit="return submitForm();">

    <h2>$addPost</h2>
    
END;

    $body .= templates_draw(array(
                                'context' => 'databoxvertical',
                                'name' => $postTitle,
                                'contents' => display_input_field(array("new_weblog_title",$contentTitle,"text"))
                            )
                            );
                            
    $body .= templates_draw(array(
                                'context' => 'databoxvertical',
                                'name' => $postBody,
                                'contents' => display_input_field(array("new_weblog_post",$contentBody,"weblogtext"))
                            )
                            );
                            
    $body .= run("weblogs:posts:edit:fields:files",$_SESSION['userid']);
    
//    if (! stripos($_SERVER['HTTP_USER_AGENT'],"Safari")) //no rich text in Safari - no worky
    $body .= <<< END
    <p>
      Embed an external video or other widget:<br />(Copy and paste embed code from external web site)<br />
                <span id="embed"><textarea name="weblog_embed_object" id="weblog_embed_object" rows="3" cols="40"></textarea>
                <input type="button" value="Embed" onclick="tinyMCE.execCommand('mceInsertRawHTML',true,this.form.weblog_embed_object.value);tinyMCE.execCommand('mceCleanup');" /></span>
            </p>
END;

    $body .= templates_draw(array(
                                'context' => 'databoxvertical',
                                'name' => $Keywords . "<br />" . $keywordDesc,
                                'contents' => display_input_field(array("new_weblog_keywords","","keywords","weblog"))
                            )
                            );

    $body .= templates_draw(array(
                                'context' => 'databoxvertical',
                                'name' => $accessRes,
                                'contents' => run("display:access_level_select",array("new_weblog_access",default_access))
                            )
                            );

    $body .= run("weblogs:posts:add:fields",$_SESSION['userid']);
    $body .= <<< END
    <p>
        <input type="hidden" name="action" value="weblogs:post:add" />
        <input type="submit" value="$postButton" />
    </p>

</form>
END;

    } else {
        
        $run_result .= "<p>" . gettext("You must be logged in to post a new weblog entry. You may do so using the login pane to the right of the screen.") . "</p>";
        
    }

    $run_result .= $body;

?>