<?php
global $USER, $CFG;

// Actions to perform on the friends screen

$action = optional_param('action');
$friend_id = optional_param('friend_id',0,PARAM_INT);
$friend = get_record('users','ident',$friend_id);

switch ($action) {
    
    // Friend someone
    case "friend":
        if (!empty($friend) && logged_on) {
            $friendalready = record_exists('friends','owner',$USER->ident,'friend',$friend_id);
            $requestedalready = record_exists('friends','owner',$USER->ident,'friend',$friend_id);
            if (empty($friendalready) && empty($requestedalready)) {
                $f = new StdClass;
                $f->owner = $USER->ident;
                $f->friend = $friend_id;
                if ($friend->moderation == 'no') {
                    if (insert_record('friends',$f)) {
                        if (run("users:type:get", $friend_id) == "person") {
                            $messages[] = sprintf(gettext("%s was added to your friends list."),$friend->name);
                            if (run("users:flags:get",array("emailnotifications",$friend_id))) {
                                $u = get_record('users','ident',$friend_id);
                                $email_message = sprintf(gettext("%s has added you as a friend!\n\nTo visit this user's profile, click on the following link:\n\n\t".
                                                                 "%s\n\nTo view all your friends, click here:\n\n\t%s\n\nRegards,\n\nThe %s team."),
                                                                 $_SESSION['name'], $CFG->wwwroot . run("users:id_to_name",$USER->ident) . "/", $CFG->wwwroot . run("users:id_to_name",$friend_id) . "/friends/",$CFG->sitename);
                                email_to_user($u,null,sprintf(gettext("New %s friend"), $CFG->sitename),$email_message);
                            }
                        }
                    } else {
                        if (run("users:type:get", $friend_id) == "person") {
                            $messages[] = sprintf(gettext("%s couldn't be added to your friends list."),$friend->name);
                        }
                    }
                } else if ($friend->moderation == 'yes') {
                    if (insert_record('friends_requests',$f)) {
                        if (run("users:type:get", $friend_id) == "person") {
                            $messages[] = sprintf(gettext("%s has elected to moderate friendship requests. Your request has been added to their moderation queue."),$friend->name);
                            if (run("users:flags:get",array("emailnotifications",$friend_id))) {
                                $u = get_record('users','ident',$friend_id);
                                $email_message = sprintf(gettext("%s has requested to add you as a friend!\n\nTo visit this user's profile, click on the following link:\n\n\t".
                                                                 "%s\n\nTo view all your friends requests and approve or deny this user, click here:\n\n\t%s\n\nRegards,\n\nThe %s team."),
                                                                 $_SESSION['name'], $CFG->wwwroot . run("users:id_to_name",$USER->ident) . "/", $CFG->wwwroot . "_friends/requests.php?owner=" . $friend_id,$CFG->sitename);
                                email_to_user($u,null,sprintf(gettext("New %s friend request"), $CFG->sitename),$email_message);
                            }
                        }
                    } else {
                        if (run("users:type:get", $friend_id) == "person") {
                            $messages[] = sprintf(gettext("%s has elected to moderate friendship requests, but your friend request couldn't be added to their moderation queue."),$friend->name);
                        }
                    }
                } else if ($friend->moderation == 'priv'  && run("users:type:get", $friend_id) == "person") {
                    $messages[] = sprintf(gettext("%s has decided not to allow any new friendship requests at this time. Your friendship request has been declined."),$friend->name);
                }
            }
        }
        break;
        // Unfriend someone
     case "unfriend":        
         if (!empty($friend) && logged_on) {
             if (delete_records('friends','owner',$USER->ident,'friend',$friend_id)) {
                 if (run("users:type:get", $friend_id) == "person") {
                     $messages[] = $friend->name . gettext(" was removed from your friends.");
                 }
             } else {
                 if (run("users:type:get", $friend_id) == "person") {
                     $messages[] = $friend->name . gettext(" couldn't be removed from your friends.");
                 }
             }
         }
         break;
                // Approve a friendship request
     case "friends:approve:request":
         $request_id = optional_param('request_id',0,PARAM_INT);
         if (!empty($request_id) && logged_on && run("users:type:get", $page_owner) == "person") { 
             if ($request = get_record_sql('SELECT u.name, fr.owner, fr.friend FROM '.$CFG->prefix.'friends_requests fr
                                    LEFT JOIN '.$CFG->prefix.'users u ON u.ident = fr.owner 
                                    WHERE fr.ident = ?',array($request_id))) {
                 if (run("permissions:check",array("userdetails:change", $page_owner))) {
                     $f = new StdClass;
                     $f->owner = $request->owner;
                     $f->friend = $request->friend;
                     if (insert_record('friends',$f)) {
                         delete_records('friends_requests','ident',$request_id);
                         $messages[] = sprintf(gettext("You approved the friendship request. %s now lists you as a friend."),stripslashes($request->name));
                     } else {
                         $messages[] = gettext("An error occurred: couldn't add you as a friend");
                     }
                 } else {
                     $messages[] = gettext("Error: you do not have authority to modify this friendship request.");
                 }
             } else {
                 $messages[] = gettext("An error occurred: the friendship request could not be found.");
             }
             
         }
         break;
         // Reject a friendship request
     case "friends:decline:request":
         $request_id = optional_param('request_id',0,PARAM_INT);
         if (!empty($request_id) && logged_on && run("users:type:get", $page_owner) == "person") {
             if ($request = get_record_sql('SELECT u.name, fr.owner, fr.friend FROM '.$CFG->prefix.'friends_requests fr
                                    LEFT JOIN '.$CFG->prefix.'users u ON u.ident = fr.owner 
                                    WHERE fr.ident = ?',array($request_id))) {
                 if (run("permissions:check",array("userdetails:change", $page_owner))) {
                     delete_records('friends_requests','ident',$request_id);
                     $messages[] = sprintf(gettext("You declined the friendship request. %s does not list you as a friend."),stripslashes($request->name));
                 } else {
                     $messages[] = gettext("Error: you do not have authority to modify this friendship request.");
                 }
             } else {
                 $messages[] = gettext("An error occurred: the friendship request could not be found.");
             }
             
         }
         break;
}         

?>