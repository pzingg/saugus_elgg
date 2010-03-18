<?php
    Class Post extends ElggObject
    {
        var $weblog;
        var $blog_id;
        var $access = 'PUBLIC';
        var $posted;
        var $title;
        var $body;
        var $tags;
        var $comments;
        var $type = 'post';

        var $exists;

        /**
         *
         */
        function Post($post_id = null)
        {
            $this->exists = false;

            // Parameter passed, assume an existing post
            if ($post_id != null)
            {
                if ($post = get_record('weblog_posts','ident',$post_id)) {
                    $this->ident     = $post->ident;
                    $this->owner     = $post->owner;
                    $this->blog_id   = $post->weblog;
                    $this->access    = $post->access;
                    $this->posted    = $post->posted;
                    $this->title     = $post->title;
                    $this->body      = $post->body;
                    
                    // Get the weblog context
                    $this->weblog = run('weblogs:instance', array('user_id' => $this->owner,
                                                                  'blog_id' => $this->blog_id));
                    
                    // Does the requested id exist
                    $this->exists = true;
                }
                
                if ($post_tags = get_records('tags','tagtype','weblog','ref',$post_id)) {
                    // An aray of Tag objects
                    foreach ($post_tags as $tag) {
                        $this->tags[] = $tag->ident;
                    }
                }
                
                if ($post_comments = get_records('weblog_comments','post_id',$post_id)) {
                    foreach ($post_comments as $comment) {
                        $this->comments[] = $comment->ident;
                    }
                }
            }
        }

        /**
         *
         */
        function getCommunity()
        {
            return $this->community;
        }

        /**
         *
         */
        function getAccess()
        {
            return $this->access;
        }

        /**
         *
         */
        function getPosted()
        {
            return $this->posted;
        }

        /**
         *
         */
        function getTitle()
        {
            return $this->title;
        }

        /**
         *
         */
        function getBody()
        {
            return $this->body;
        }

        /**
         *
         */
        function getWeblog()
        {
            return $this->blog_id;
        }

        /**
         *
         */
        function getTags()
        {
            return $this->tags;
        }

        /**
         *
         */
        function deleteTags()
        {
            $value = false;

            foreach ($this->tags as $tag_id)
            {
                $tag = run('tags:instance', array('id' => $tag_id));
                $value = $tag->delete();
            }

            return $value;
        }

        /**
         *
         */
        function getTag($tag_id)
        {
            return run('tags:instance', array("id" => $tag_id));
        }

        /**
         *
         */
        function getComments()
        {
            return $this->comments;
        }
        
        /**
         *
         */
        function getComment($comment_id)
        {
            return run('comments:instance', array('id' => $comment_id));
        }

        /**
         *
         */
        function deleteComments()
        {
            $value = false;

            foreach ($this->comments as $comment_id)
            {
                $comment = run('comments:instance', array('id' => $comment_id));
                $value = $comment->delete();
            }

            return $value;
        }

        /**
         *
         */
        function getUrl()
        {
            return $this->weblog->getUrl() ."#" . $this->ident;
        }

        /**
         *
         */
        function getPermaLink()
        {
            return $this->weblog->getUrl() . $this->ident . ".html";
        }

        /**
         *
         */
        function setOwner($val)
        {
            $this->owner = $val;
        }

        /**
         *
         */
        function setCommunity($val)
        {
            $this->community = $val;
        }

        /**
         *
         */
        function setAccess($val)
        {
            $this->access = $val;
        }

        /**
         *
         */
        function setPosted($val)
        {
            $this->posted = $val;
        }

        /**
         *
         */
        function setTitle($val)
        {
            $this->title = $val;
        }

        /**
         *
         */
        function setBody($val)
        {
            $this->body = $val;
        }

        /**
         *
         */
        function setWeblog($val)
        {
            $this->blog_id = $val;
        }

        /**
         *
         */
        function delete()
        {
            if ($this->exists)
            {
                // Check ownership
                if ($this->weblog->isOwner() != true)
                {
                    // Not weblog owner, check at post level
                    if ($this->owner != $this->weblog->getOwner())
                    {
                        return false;
                    }
                }

                // Remove related objects

                // Remove tags
                $this->deleteTags();

                // Remove comments
                $this->deleteComments();
                return delete_records('weblog_posts','ident',$this->ident);
            }
        }

        /**
         *
         */
        function save()
        {
            $wp = new StdClass;
            $wp->title = $this->title;
            $wp->body = $this->body;
            $wp->access = $this->access;
            $wp->ident = $this->ident;
            if ($this->exists == true)
            {
                // Check ownership
                if ($this->weblog->isOwner() != true)
                {
                    // Not weblog owner, check at post level
                    if ($this->owner != $this->weblog->getOwner())
                    {
                        return false;
                    }
                }

                if (update_record('weblog_posts',$wp)) {
                    return $this->ident;
                } 
                return false;
            }
            else
            {
                $wp->weblog = $this->blog_id;
                $wp->posted = time();
                $wp->owner = $this->owner;
                if ($this->ident = insert_record('weblog_posts',$wp)) {
                    $this->exists = true;

                    return $this->ident;
                }
                else
                {
                    return false;
                }
            }
        }
    }

?>
