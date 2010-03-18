<?php

if (isset($parameter)) {
        
    $post = $parameter;
    
    global $post_authors;
    global $individual;
    global $CFG;
    
    //if (!isset($post_authors[$post->owner])) {
        
        $author = "";
        
        $stuff = get_record('users','ident',$post->owner);
        
        $author->fullname = htmlspecialchars($stuff->name, ENT_COMPAT, 'utf-8');
        
        if ($stuff->icon == -1 || $post->owner == -1) {
            $author->icon = 0;
        } else {
            $icon = get_record('icons','ident',$stuff->icon);
            $author->icon = $icon->ident;
        }
        
        $post_authors[$post->owner] = $author;
        
    //}
    //if (!isset($post->authors[$post->weblog])) {
        $community = "";
        
        $stuff2 = get_record('users','ident',$post->weblog);
        
        $community->fullname = htmlspecialchars($stuff2->name, ENT_COMPAT, 'utf-8');
        
        if (empty($stuff2->icon) || $stuff2->icon == -1) {
            $community->icon = 0;
        } else {
            $icon = get_record('icons','ident',$stuff2->icon);
            $community->icon = $icon->ident;
        }
        
        $post_authors[$post->weblog] = $community;
    //}
    
    $date = gmdate("H:i",$post->posted);
    
        $username = run("users:id_to_name",$post->owner);
    
    
    // Allow plugins to set special icons
    $specialicon = run("weblogs:posts:geticon",$post);
    
    // If there is no special icon for this post, set to the default
    if ($specialicon == NULL) {
        $usericon = $post_authors[$post->owner]->icon;
        if ($usericon == "default.png") {
            $usericon = $post_authors[$post->weblog]->icon;
        }    
    } else {
        $usericon = $specialicon;
    }
    
    // Allow plugins to set the name on the post
    $specialname = run("weblogs:posts:getname",$post);
    if (empty($specialname)) {
        $fullname = $post_authors[$post->owner]->fullname;
    } else {
        $fullname = $specialname;
    }
    
    $title = get_access_description($post->access);
    $title .= htmlspecialchars($post->title, ENT_COMPAT, 'utf-8');
    
    if ($post->owner != $post->weblog) {
        
        if ($post_authors[$post->owner]->icon == -1) {
            $usericon = $post_authors[$post->weblog]->icon;
        }

        $fullname .= " @ " . $post_authors[$post->weblog]->fullname;
        $username = run("users:id_to_name",$post->weblog);
    }
    
    $body = run("weblogs:text:process", $post->body);
    $More = gettext("More");
    $Keywords = gettext("Keywords:");
    $anyComments = gettext("comment(s)");
    $body = str_replace("{{more}}","<a href=\"" . url .$username."/weblog/{$post->ident}.html\">$More ...</a>",$body);
    $keywords = display_output_field(array("","keywords","weblog","weblog",$post->ident,$post->owner));

    if ($keywords) {
        $body .= <<< END
            <div class="weblog_keywords">
            <p>
                $Keywords {$keywords}
            </p>
            </div>
END;
    }
    // if ($post->owner == $_SESSION['userid'] && logged_on) {
    if (run("permissions:check",array("weblog:edit",$post->owner))) {
        $Edit = gettext("Edit");
        $returnConfirm = gettext("Are you sure you want to permanently delete this weblog post?");
        $Delete = gettext("Delete");
        $body .= <<< END
            
            <div class="blog_edit_functions">
                <p>
                    [<a href="{$CFG->wwwroot}_weblog/edit.php?action=edit&amp;weblog_post_id={$post->ident}&amp;owner={$post->owner}">$Edit</a>]
                    [<a href="{$CFG->wwwroot}_weblog/action_redirection.php?action=delete_weblog_post&amp;delete_post_id={$post->ident}" onclick="return confirm('$returnConfirm')">$Delete</a>]
                </p>
            </div>
            
END;
    }
    
    if (!isset($_SESSION['comment_cache'][$post->ident]) || (time() - $_SESSION['comment_cache'][$post->ident]->created > 120)) {
        $numcomments = count_records('weblog_comments','post_id',$post->ident);
        $_SESSION['comment_cache'][$post->ident]->created = time();
        $_SESSION['comment_cache'][$post->ident]->data = $numcomments;
    }
    $numcomments = $_SESSION['comment_cache'][$post->ident]->data;
    
    $comments = "<a href=\"".url.$username."/weblog/{$post->ident}.html\">$numcomments $anyComments</a>";        
    
    if (isset($individual) && ($individual == 1)) {
        // looking at an individual post and its comments

        $commentsbody = "";
            
        if ($post->ident > 0) {
            // if post exists and is visible
            
            if ($comments = get_records('weblog_comments','post_id',$post->ident,'posted ASC')) {
                foreach($comments as $comment) {
                    $commentmenu = "";
                    if (logged_on && ($comment->owner == $_SESSION['userid'] || run("permissions:check", "weblog"))) {
                        $Edit = gettext("Edit");
                        $returnConfirm = gettext("Are you sure you want to permanently delete this weblog comment?");
                        $Delete = gettext("Delete");
                        $commentmenu = <<< END

                <p>
                        [<a href="{$CFG->wwwroot}_weblog/action_redirection.php?action=weblog_comment_delete&amp;weblog_comment_delete={$comment->ident}" onclick="return confirm('$returnConfirm')">$Delete</a>]
                </p>
END;
                    }
                    $comment->postedname = htmlspecialchars($comment->postedname, ENT_COMPAT, 'utf-8');
                    
                    // turn commentor name into a link if they're a registered user
                    // add rel="nofollow" to comment links if they're not
                    if ($comment->owner > 0) {
                        $commentownerusername = run("users:id_to_name",$comment->owner);
                        $comment->postedname = '<a href="' . url . $commentownerusername . '/">' . $comment->postedname . '</a>';
                        $comment->icon = '<a href="' . url . $commentownerusername . '/">' . "<img src=\"" . $CFG->wwwroot . $commentownerusername . "/icons/" . run("icons:get",$comment->owner) . "/w/50/h/50/\" border=\"0\" align=\"left\" alt=\"\" /></a>";
                        $comment->body = run("weblogs:text:process", array($comment->body, false));
                    } else {
                        $comment->icon = "<img src=\"" . $CFG->wwwroot . "_icons/data/default.png\" width=\"50\" height=\"50\" align=\"left\" alt=\"\" />";
                        $comment->body = run("weblogs:text:process", array($comment->body, true));
                    }
                    
                    $commentsbody .= templates_draw(array(
                                                          'context' => 'weblogcomment',
                                                          'postedname' => $comment->postedname,
                                                          'body' => $comment->body . $commentmenu,
                                                          'posted' => strftime("%A, %e %B %Y, %R %Z",$comment->posted),
                                                          'usericon' => $comment->icon
                                                          )
                                                    );
                    
                }
                $commentsbody = templates_draw(array(
                                                     'context' => 'weblogcomments',
                                                     'comments' => $commentsbody
                                                     )
                                               );
                
            }
            
            $run_result .= templates_draw(array(
                                                'context' => 'weblogpost',
                                                'date' => $date,
                                                'username' => $username,
                                                'usericon' => $usericon,
                                                'body' => $body,
                                                'fullname' => $fullname,
                                                'title' => "<a href=\"".url.$username."/weblog/{$post->ident}.html\">$title</a>",
                                                'comments' => $commentsbody
                                                )
                                          );
            
            if (logged_on || run("users:flags:get",array("publiccomments",$post->owner))) {
                $run_result .= run("weblogs:comments:add",$post);
            } else {
                $run_result .= "<p>" . gettext("You must be logged in to post a comment.") . "</p>";
            }
            
                $run_result .= run("weblogs:interesting:form",$post->ident);
                
        } else {
            // post is missing or prohibited
            
            $run_result .= templates_draw(array(
                                                'context' => 'weblogpost',
                                                'date' => "",
                                                'username' => "",
                                                'usericon' => "default.png",
                                                'body' => $body,
                                                'fullname' => "",
                                                'title' => "<a href=\"".url.$username."/weblog/{$post->ident}.html\">$title</a>",
                                                'comments' => ""
                                                )
                                          );
        }
    } else {
        
        $run_result .= templates_draw(array(
                                            'context' => 'weblogpost',
                                            'date' => $date,
                                            'username' => $username,
                                            'usericon' => $usericon,
                                            'body' => $body,
                                            'fullname' => $fullname,
                                            'title' => "<a href=\"".url.$username."/weblog/{$post->ident}.html\">$title</a>",
                                            'commentslink' => $comments
                                            )
                                      );        
    }
}

?>