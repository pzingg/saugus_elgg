<?php

    if (isset($parameter)) {
    
        $post = $parameter;
        
        $addComment = gettext("Add a comment"); // gettext variable
        $accessRes = gettext("Comment visibility:"); // gettext variable
        $run_result .= <<< END
        
    <form action="" method="post">
    
        <h2>$addComment</h2>
    
END;

        $field = display_input_field(array("new_weblog_comment","","longtext"));
        if (logged_on) {
            $userid = $_SESSION['userid'];
        } else {
            $userid = -1;
        }
        $field .= <<< END
        
        <input type="hidden" name="action" value="weblogs:comment:add" />
        <input type="hidden" name="post_id" value="{$post->ident}" />
        <input type="hidden" name="owner" value="{$userid}" />
        
END;
        
        $run_result .= templates_draw(array(
        
                                'context' => 'databox1',
                                'name' => gettext("Your comment text"),
                                'column1' => $field
        
                            )
                            );
                            
        if (logged_on) {
            $comment_name = $_SESSION['name'];
        } else {
            $comment_name = gettext("Guest");
        }

        $run_result .= templates_draw(array(
        
                                'context' => 'databox1',
                                'name' => gettext("Your name"),
                                'column1' => "<input type=\"text\" name=\"postedname\" value=\"".htmlspecialchars($comment_name, ENT_COMPAT, 'utf-8')."\" />"
        
                            )
                            );
                            
        $run_result .= templates_draw(array(
                                'context' => 'databox1',
                                'name' => $accessRes,
                                'column1' => run("display:access_level_select",array("edit_comment_access",$post->access,$post->owner))
                            )
                            );

        //captcha
        $run_result .= run('mod:captcha:display');
        
        $run_result .= templates_draw(array(
        
                                'context' => 'databox1',
                                'name' => '&nbsp;',
                                'column1' => "<input type=\"submit\" value=\"".gettext("Add comment")."\" />"
        
                            )
                            );
                            
        $run_result .= <<< END
    
    </form>
        
END;
        
    }

?>