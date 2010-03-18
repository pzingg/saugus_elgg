<?php

// $username = $_SESSION['username'];
$post = get_record('weblog_posts','ident',$parameter);

global $CFG;
global $page_owner;
$page_owner = $post->weblog;

$username = run("users:id_to_name", $post->weblog);

if (!run("permissions:check", array("weblog:edit",$post->owner))) {
    exit();
}

$editPost = gettext("Edit a post");
$postTitle = gettext("Post title:");
$postBody = gettext("Post body:");
$Keywords = gettext("Keywords (Separated by commas):"); // gettext variable
$keywordDesc = gettext("Keywords commonly referred to as 'Tags' are words that represent the weblog post you have just made. This will make it easier for others to search and find your posting."); // gettext variable
$accessRes = gettext("Access restrictions:"); // gettext variable
$postButton = gettext("Save Post"); // gettext

$body = <<< END

<form method="post" name="elggform" action="{$CFG->wwwroot}{$username}/weblog/{$post->ident}.html" onsubmit="return submitForm();">

    <h2>$editPost</h2>
END;
    
    $body .= templates_draw(array(
                                'context' => 'databoxvertical',
                                'name' => $postTitle,
                                'contents' => display_input_field(array("edit_weblog_title",stripslashes($post->title),"text"))
                            )
                            );
                            
    $body .= templates_draw(array(
                                'context' => 'databoxvertical',
                                'name' => $postBody,
                                'contents' => display_input_field(array("new_weblog_post",stripslashes($post->body),"weblogtext"))
                            )
                            );

    $body .= templates_draw(array(
                                'context' => 'databoxvertical',
                                'name' => $Keywords . "<br />" . $keywordDesc,
                                'contents' =>  display_input_field(array("edit_weblog_keywords","","keywords","weblog",$post->ident))
                            )
                            );

    $body .= templates_draw(array(
                                'context' => 'databoxvertical',
                                'name' => $accessRes,
                                'contents' => run("display:access_level_select",array("edit_weblog_access",$post->access))
                            )
                            );

    $body .= run("weblogs:posts:edit:fields",array($_SESSION['userid'], $post->ident));
    $body .= <<< END
    <p>
        <input type="hidden" name="action" value="weblogs:post:edit" />
        <input type="hidden" name="edit_weblog_post_id" value="{$post->ident}" />
        <input type="submit" value="$postButton" />
    </p>

</form>
END;

$run_result .= $body;

?>