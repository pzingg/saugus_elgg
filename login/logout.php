<?php

require_once(dirname(dirname(__FILE__))."/includes.php");

$user->code = '';
$user->ident = $USER->ident;
update_record('users',$user);

unset($USER);
unset($SESSION);
unset($_SESSION['USER']);
unset($_SESSION['SESSION']);
unset($_SESSION['userid']);
unset($_SESSION['username']);
unset($_SESSION['name']);
unset($_SESSION['email']);
unset($_SESSION['icon']);
unset($_SESSION['icon_quota']);

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-84600, '/');
}

// Remove the any AUTH_COOKIE persistent logins
setcookie(AUTH_COOKIE, '', time()-84600, '/');

session_destroy();

// Set headers to forward to main URL
header("Location: " . url . "\n");

    
?>