<?php
global $CFG,$USER;
global $user_type;

// If we've been passed a valid user ID as a parameter ...
if (isset($parameter) && (isset($parameter[0])) && ($parameter[0] != $_SESSION['userid']) && logged_on) {
    
    $user_id = (int) $parameter[0];
    
    if (run("users:type:get", $user_id) == "person") {
        
        if (!count_records_sql('SELECT COUNT(u.ident) FROM '.$CFG->prefix.'friends f
                                   JOIN '.$CFG->prefix.'users u ON u.ident = f.friend
                                   WHERE f.owner = ? AND f.friend = ?',array($USER->ident,$user_id))) {
            $moderation = get_field('users','moderation','ident',$user_id);
            switch ($moderation) {
            case 'no':
            case 'yes':
                $run_result = "<a href=\"".url."_friends/index.php?friends_name=".$_SESSION['username']."&amp;action=friend&amp;friend_id=$user_id\" onclick=\"return confirm('". gettext("Are you sure you want to add this user as a friend?") ."')\">" . gettext("Click here to add this user as a friend."). "</a>";
                break;
            case 'priv':
                $run_result = '';
                break;
            }
        } else {
            $run_result = "<a href=\"".url."_friends/index.php?friends_name=".$_SESSION['username']."&amp;action=unfriend&amp;friend_id=$user_id\" onclick=\"return confirm('". gettext("Are you sure you want to remove this user from your friends list?") ."')\">" . gettext("Click here to remove this user from your friends list."). "</a>";
        }
    }
}

?>