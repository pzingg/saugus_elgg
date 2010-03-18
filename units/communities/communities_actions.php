<?php

global $CFG;
global $USER;
global $page_owner;
global $friend;

// Actions to perform on the friends screen
$action = optional_param('action');
$friend_id = optional_param('friend_id',0,PARAM_INT);

switch($action) {

    // Create a new community
    case "community:create":
        $comm_name = optional_param('comm_name');
        $comm_username = optional_param('comm_username');
        if (logged_on && !empty($comm_name) && !empty($comm_username)) {
            if (!preg_match("/^[A-Za-z0-9]{3,12}$/",$comm_username)) {
                $messages[] = gettext("Error! The community username must contain letters and numbers only, cannot be blank, and must be between 3 and 12 characters in length.");
            } else if (trim($comm_name) == "") {
                $messages[] = gettext("Error! The community name cannot be blank.");
            } else {
                $comm_username = strtolower(trim($comm_username));
                if (record_exists('users','username',$comm_username)) {
                    $messages[] = sprintf(gettext("The username %s is already taken by another user. You will need to pick a different one."), $comm_username);
                } else {
                    $name = trim($comm_name);
                    $c = new StdClass;
                    $c->name = $name;
                    $c->username = $comm_username;
                    $c->user_type = 'community';
                    $c->owner = $USER->ident;
                    $cid = insert_record('users',$c);
                    
                    $rssresult = run("weblogs:rss:publish", array($cid, false));
                    $rssresult = run("files:rss:publish", array($cid, false));
                    $rssresult = run("profile:rss:publish", array($cid, false));
                    
                    $f = new StdClass;
                    $f->owner = $USER->ident;
                    $f->friend = $cid;
                    insert_record('friends',$f);
                    $messages[] = gettext("Your community was created and you were added as its first member.");
                }
            }
        }

        // There is deliberately not a break here - creating a community should automatically make you a member.
        
    // Friend someone
     case "friend":
         if (!empty($friend_id) && logged_on) {
             if (run("users:type:get", $friend_id) == "community") {
                 if ($friend = get_record('users','ident',$friend_id)) {
                     $owner = get_record('users','ident',$friend->owner);
                     if ($friend->moderation == "no") {
                         $messages[] = sprintf(gettext("You joined %s."), stripslashes($friend->name));
                         if (run("users:flags:get",array("emailnotifications",$owner->ident))) {
                             $email_message = sprintf(gettext("%s has joined %s!\n\nTo visit this user's profile, click on the following link:\n\n\t".
                                                                     "%s\n\nTo view all community members, click here:\n\n\t%s\n\nRegards,\n\nThe %s team."),
                                                                     $_SESSION['name'], $friend->name, $CFG->wwwroot . run("users:id_to_name",$USER->ident) . "/", $CFG->wwwroot . "_communities/members.php?owner=" . $friend_id,$CFG->sitename);
                             email_to_user($owner,null,sprintf(gettext("New %s member"), $friend->name),$email_message);
                         }
                     } else if ($friend->moderation == "yes") {
                         $messages[] = sprintf(gettext("Membership of %s needs to be approved. Your request has been added to the list."), stripslashes($friend->name));
                         if (run("users:flags:get",array("emailnotifications",$owner->ident))) {
                             $email_message = sprintf(gettext("%s has applied to join %s!\n\nTo visit this user's profile, click on the following link:\n\n\t".
                                                                     "%s\n\nTo view all membership requests and approve or deny this user, click here:\n\n\t%s\n\nRegards,\n\nThe %s team."),
                                                                     $_SESSION['name'], $friend->name, $CFG->wwwroot . run("users:id_to_name",$USER->ident) . "/", $CFG->wwwroot . "_communities/members.php?owner=" . $friend_id,$CFG->sitename);
                             email_to_user($owner,null,sprintf(gettext("New %s member"), $friend->name),$email_message);
                         }
                     } else if ($friend->moderation == "priv") {
                         $messages[] = sprintf(gettext("%s is a private community. Your membership request has been declined."), stripslashes($friend->name));
                     }
                 }
             }
         }
         break;
         
     // Unfriend someone
     case "unfriend":
         if (!empty($friend_id) && logged_on) {
             if (run("users:type:get", $friend_id) == "community") {
                 $name = run("users:id_to_name",$friend_id);
                 $messages[] = sprintf(gettext("You left %s."), $name);
             }
         }
         break;
         
    case "weblogs:post:add":
        if (run("users:type:get",$page_owner) == "community") {
            $messages[] = gettext("Your post has been added to the community weblog.");
        }
        break;

        // Approve a membership request
    case "community:approve:request":
         $request_id = optional_param('request_id',0,PARAM_INT);
         if (!empty($request_id) && logged_on && run("users:type:get", $page_owner) == "community") {
             if ($request = get_record_sql('SELECT u.name,fr.owner,fr.friend FROM '.$CFG->prefix.'friends_requests fr LEFT JOIN '.$CFG->prefix.'users u ON u.ident = fr.owner
                                            WHERE fr.ident = ?',array($request_id))) {
                 if (run("permissions:check",array("userdetails:change", $page_owner))) {
                     $f = new StdClass;
                     $f->owner = $request->owner;
                     $f->friend = $request->friend;
                     if (insert_record('friends',$f)) {
                         delete_records('friends_requests','ident',$request_id);
                         $messages[] = sprintf(gettext("You approved the membership request. %s is now a member of this community."),stripslashes($request->name));
                     } else {
                         $messages[] = gettext("An error occurred: the membership request could not be approved.");
                     }
                 } else {
                     $messages[] = gettext("Error: you do not have authority to modify this membership request.");
                 }
             } else {
                 $messages[] = gettext("An error occurred: the membership request could not be found.");
             }
             
         }
         break;

         // Reject a membership request
     case "community:decline:request":
         $request_id = optional_param('request_id',0,PARAM_INT);
         if (!empty($request_id) && logged_on && run("users:type:get", $page_owner) == "community") {
             if ($request = get_record_sql('SELECT u.name,fr.owner,fr.friend FROM '.$CFG->prefix.'friends_requests fr LEFT JOIN '.$CFG->prefix.'users u ON u.ident = fr.owner
                                            WHERE fr.ident = ?',array($request_id))) {
                 if (run("permissions:check",array("userdetails:change", $page_owner))) {
                     delete_records('friends_requests','ident',$request_id);
                     $messages[] = sprintf(gettext("You declined the membership request. %s is not a member of this community."),stripslashes($request->name));
                 } else {
                     $messages[] = gettext("Error: you do not have authority to modify this membership request.");
                 }
             } else {
                 $messages[] = gettext("An error occurred: the membership request could not be found.");
             }
             
         }
         break;
         
}
?>