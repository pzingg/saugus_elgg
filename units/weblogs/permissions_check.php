<?php

    // Last modified Ben Werdmuller May 19 2005

    // Check permissions
    // run("permissions:check", "object");
    
    // To add permission functionality to your own units, add code to
    // $function['permissions:check'] in your main.php
    
    // You can use this file as a template; $page_owner should already
    // be set - if the user is on a page specific to your unit, it should
    // be set in run("your_unit_name:init")
    
        global $page_owner;
        global $USER;
        global $CFG;
        
        if (($parameter == "weblog") || ($parameter == "weblog:comment")) {
            
            if ($page_owner == $_SESSION['userid'] && logged_on) {
                $run_result = true;
            }
            
        }
        
        if (logged_on) {
            
            // $parameter[0] = context
            // $parameter[1] = $post->owner
            
            if ($parameter[0] == "weblog:edit") {
                
                if ($parameter[1] == $_SESSION['userid'] && logged_on) {
                    $run_result = true;
                }
                
            }
            
        }
        
        if ($parameter[0] == "weblog:comment") {
            
             // $parameter[0] = context
             // $parameter[1] = $comment (record)
             // $parameter[2] = $post (record)
             
            $comment = $parameter[1];
            $post = $parameter[2];
            
            if (($comment->access == 'PUBLIC') || ($comment->access == 'user' . $_SESSION['userid']) || (logged_on && ($comment->access == 'LOGGED_IN' || $comment->owner == $USER->owner)) || (logged_on && $post->owner == $USER->ident && $USER->owner == -1))
            	$run_result = true;
            if ($comment->owner == $_SESSION['userid'])
            	$run_result = true;
            if ($CFG->owned_users_allaccess && $USER->owner == -1 && get_field("users","owner","ident",$post->owner) != -1)
            	$run_result = true;
        }

?>