<?php

if (isset($parameter)) {
        
    $post = $parameter;
    
    global $post_authors;
    global $individual;
    global $CFG;
    global $USER;
    
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
     //if ($post->owner == $_SESSION['userid'] && logged_on) {
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
    
//	Can't use comment cache now that we're handling access controls
//    if (!isset($_SESSION['comment_cache'][$post->ident]) || (time() - $_SESSION['comment_cache'][$post->ident]->created > 120)) {
//        $numcomments = count_records('weblog_comments','post_id',$post->ident);
//        $_SESSION['comment_cache'][$post->ident]->created = time();
//        $_SESSION['comment_cache'][$post->ident]->data = $numcomments;
//    }
//    $numcomments = $_SESSION['comment_cache'][$post->ident]->data;
    
    $where = run("users:access_level_sql_where",$_SESSION['userid']);
    if (logged_on && $USER->owner != -1 && $post->owner == $USER->ident) $where .= " or owner=" . $USER->owner . " ";
    if (logged_on && $USER->owner == -1 && $CFG->owned_users_allaccess && get_field("users","owner","ident",$post->owner) != -1)
    	$where .= " or 0=0 ";
    $numcomments = count_records_select('weblog_comments','('.$where.') AND post_id = '.$post->ident);
    
    $comments = "<a href=\"".url.$username."/weblog/{$post->ident}.html\">$numcomments $anyComments</a>";
    
    //add the nifty Add This button to the info bar under each post, if enabled in the config file
    $addthis_button = "";  
    if ($CFG->addthis_enabled) {
    $post_url = url.$username."/weblog/".$post->ident.".html";
    $post_title = str_replace("'", "\\'", $title);
    $addthis_button = <<< END
<!-- ADDTHIS BUTTON BEGIN -->
<script type="text/javascript"> 
addthis_logo_background = 'EFEFFF';
addthis_logo_color      = '666699';
addthis_options         = 'favorites, email, digg, delicious, myspace, facebook, google, live, myweb, technorati, reddit, stumbleupon, furl, more';
</script>
<span class="addthis_button"><a href="http://www.addthis.com/bookmark.php" onmouseover="return addthis_open(this, '', '$post_url', '$post_title')" onmouseout="addthis_close()" onclick="return addthis_sendto()"><img src="http://s9.addthis.com/button0-share.gif" width="83" height="16" border="0" alt="Share This" /></a></span>
<script type="text/javascript" src="http://s7.addthis.com/js/152/addthis_widget.js"></script>
<!-- ADDTHIS BUTTON END -->
    
END;

    }
    
    if (isset($individual) && ($individual == 1)) {
        // looking at an individual post and its comments
            
        if ($post->ident > 0) {
            // if post exists and is visible
            
            $commentsbody = run("weblogs:comments:view",$post);
            
            $run_result .= templates_draw(array(
                                                'context' => 'weblogpost',
                                                'date' => $date,
                                                'username' => $username,
                                                'usericon' => $usericon,
                                                'body' => $body,
                                                'fullname' => $fullname,
                                                'title' => "<a href=\"".url.$username."/weblog/{$post->ident}.html\">$title</a>",
                                                'comments' => $commentsbody,
            									'commentslink' => $addthis_button
                                                )
                                          );
            
            if (logged_on || run("users:flags:get",array("publiccomments",$post->owner))) {
//            	if ($post->access != "PUBLIC" || $USER->owner == -1) //owned users can't comment on public posts' temp fix
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
                                            'commentslink' => $comments." | ".$addthis_button
                                            )
                                      );        
    }
}

?>