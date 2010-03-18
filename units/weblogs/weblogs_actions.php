<?php
global $USER;
global $CFG;

// Actions to perform
$action = optional_param('action');
switch ($action) {
    // Create a new weblog post
    case "weblogs:post:add":
        $post = new StdClass;
        $post->title = trim(optional_param('new_weblog_title'));
        $post->body = trim(optional_param('new_weblog_post'));
        $post->access = trim(optional_param('new_weblog_access'));
        if (logged_on && !empty($post->body) && !empty($post->access) && run("permissions:check", "weblog")) {
            $post->posted = time();
            $post->owner = $USER->ident;
            $post->weblog = $page_owner;
            $insert_id = insert_record('weblog_posts',$post);
            $value = trim(optional_param('new_weblog_keywords'));
            insert_tags_from_string ($value, 'weblog', $insert_id, $post->access, $post->owner);

            $rssresult = run("weblogs:rss:publish", array($page_owner, false));
            $rssresult = run("profile:rss:publish", array($page_owner, false));
            if (run("users:type:get",$page_owner) == "person") {
                $messages[] = gettext("Your post has been added to your weblog.");
            }
            // define('redirect_url',url . $_SESSION['username'] . "/weblog/");
            define('redirect_url',url . run("users:id_to_name", $page_owner) . "/weblog/");
        }
        break;
        
    // Edit a weblog post
    case "weblogs:post:edit":
        $post = new StdClass;
        $post->title = trim(optional_param('edit_weblog_title'));
        $post->body = trim(optional_param('new_weblog_post'));
        $post->access = trim(optional_param('edit_weblog_access'));
        $post->ident = optional_param('edit_weblog_post_id',0,PARAM_INT);
        if (logged_on && !empty($post->body) && !empty($post->access) && !empty($post->ident)) {
            $exists = false;
            if ($oldpost = get_record('weblog_posts','ident',$post->ident)) {
                if (run("permissions:check", array("weblog:edit", $oldpost->owner))) {
                    $exists = true;
                }
            }
            
            if (!empty($exists)) {
                update_record('weblog_posts',$post);
                delete_records('tags','tagtype','weblog','ref',$post->ident);
                $value = trim(optional_param('edit_weblog_keywords'));
                insert_tags_from_string ($value, 'weblog', $post->ident, $post->access, $oldpost->owner);
                $rssresult = run("weblogs:rss:publish", array($oldpost->weblog, false));
                $rssresult = run("profile:rss:publish", array($oldpost->weblog, false));
                $messages[] = gettext("The weblog post has been modified."); // gettext variable
            }
        }
        break;
        
    //Mark a weblog post as interesting
    case "weblog:interesting:on":
        $weblog_post = optional_param('weblog_post',0,PARAM_INT);
        if (logged_on && !empty($weblog_post)) {
            $wl = new StdClass;
            $wl->weblog_post = $weblog_post;
            $wl->owner = $USER->ident;
            if (insert_record('weblog_watchlist',$wl)) {
                $messages[] = gettext("This weblog post has now been added to your 'interesting' list.");
            }
        }
        break;
        
    //Remove an 'interesting' flag
    case "weblog:interesting:off":
        $weblog_post = optional_param('weblog_post',0,PARAM_INT);
        if (logged_on && !empty($weblog_post)) {
            if (delete_records('weblog_watchlist','weblog_post',$weblog_post,'owner',$USER->ident)) {
                $messages[] = gettext("You are no longer monitoring this weblog post.");
            }
        }
        break;
        
    // Delete a weblog post
    case "delete_weblog_post":
        $id = optional_param('delete_post_id',0,PARAM_INT);
        if (logged_on && !empty($id)) {
            if ($post_info = get_record('weblog_posts','ident',$id)) {
                if (run("permissions:check", array("weblog:edit", $post_info->owner))) {
                    delete_records('weblog_posts','ident',$id);
                    delete_records('weblog_comments','post_id',$id);
                    delete_records('weblog_watchlist','weblog_post',$id);
                    delete_records('tags','tagtype','weblog','ref',$id);
                    $rssresult = run("weblogs:rss:publish", array($post_info->weblog, false));
                    $rssresult = run("profile:rss:publish", array($post_info->weblog, false));
                    $messages[] = gettext("The selected weblog post was deleted."); // gettext variable - NOT SURE ABOUT THIS POSITION!!!
                } else {
                    $messages[] = gettext("You do not appear to have permissions to delete this weblog post. It was not deleted."); // gettext variable
                }
            }
            global $redirect_url;
            $redirect_url = url . run("users:id_to_name",$post_info->weblog) . "/weblog/";
            define('redirect_url',$redirect_url);
        }
        break; 
        
    // Create a weblog comment
    case "weblogs:comment:add":
        $comment = new StdClass;
        $comment->post_id = optional_param('post_id',0,PARAM_INT);
        $comment->body = trim(optional_param('new_weblog_comment'));
        $comment->postedname = trim(optional_param('postedname'));
        if (!empty($comment->post_id) && !empty($comment->body) && !empty($comment->postedname)) {
            $where = run("users:access_level_sql_where",$USER->ident);
            if ($post = get_record_select('weblog_posts','('.$where.') AND ident = '.$comment->post_id)) {
                if (run("spam:check",$comment->body) != true) {
                    // If we're logged on or comments are public, add one
                    if (logged_on || run("users:flags:get",array("publiccomments",$post->owner))) {
                        $comment->owner = $USER->ident;
                        $comment->posted = time();
                        insert_record('weblog_comments',$comment);

                        // If we're logged on and not the owner of this post, add post to our watchlist
                        if (logged_on && $comment->owner != $post->owner) {
                            delete_records('weblog_watchlist','weblog_post',$comment->post_id,'owner',$comment->owner);
                            $wl = new StdClass;
                            $wl->owner = $comment->owner;
                            $wl->weblog_post = $comment->post_id;
                            insert_record('weblog_watchlist',$wl);
                        }

                        // Email comment if applicable
                        if (run("users:flags:get",array("emailreplies",$post->owner))) {
                            if ($email = get_record('users','ident',$post->owner)) {
                                $username = $email->username;
                                $message = gettext(sprintf("You have received a comment from %s on your blog post '%s'. It reads as follows:", $comment->postedname, stripslashes($post->title)));
                                $message .= "\n\n\n" . stripslashes($comment->body) . "\n\n\n";
                                $message .= gettext(sprintf("To reply and see other comments on this blog post, click here: %s", url . $username . "/weblog/" . $post->ident . ".html"));
                                $message = wordwrap($message);
                                $from = new StdClass;
                                $from->email = $CFG->noreplyaddress;
                                $from->name = $comment->postedname;
                                email_to_user($email,$from,stripslashes($post->title),$message);
                            }
                        }
                        $messages[] = gettext("Your comment has been added."); // gettext variable
                    }
                } else {
                    $messages[] = gettext("Your comment could not be posted. The system thought it was spam.");
                }
            }
        }
        break;
        
        
    // Delete a weblog comment
    case "weblog_comment_delete":
        $comment_id = optional_param('weblog_comment_delete');
        if (logged_on && !empty($comment_id)) {
            $commentinfo = get_record_sql('SELECT wc.*,wp.owner AS postowner,wp.ident AS postid
                                           FROM '.$CFG->prefix.'weblog_comments wc 
                                           LEFT JOIN '.$CFG->prefix.'weblog_posts wp ON wp.ident = wc.post_id
                                            WHERE wc.ident = ' . $comment_id);
            if ($commentinfo->owner == $USER->ident || run("permissions:check", "weblog")) {
                delete_records('weblog_comments','ident',$comment_id);
                $messages[] = gettext("Your comment was deleted.");
            }
            $redirect_url = url . run("users:id_to_name",$commentinfo->postowner) . "/weblog/" . $commentinfo->postid . ".html";
            define('redirect_url',$redirect_url);
        }
        break;
}

?>
