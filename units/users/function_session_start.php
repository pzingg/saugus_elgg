<?php
global $USER;
// Check to see if authorization is needed (check cookie)
$logged_in = isloggedin();

//TODO should probably go but maybe we should keep it
// for backwards compatibility
// Set logged-in status in stone
define('logged_on', $logged_in);

// If we're not logged in, set the user ID to -1
if (logged_on) {
    // Update the 'last action' time counter to now for the current user
    $user = new StdClass;
    $user->last_action = time();
    $user->ident = $USER->ident;
    update_record('users',$user);
}

?>
