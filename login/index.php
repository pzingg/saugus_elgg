<?php

define("context","login");

require_once(dirname(dirname(__FILE__)).'/includes.php');
global $CFG;

$redirect_url = trim(optional_param('passthru_url'));
if (empty($redirect_url)) {
    $redirect_url = $CFG->wwwroot . "index.php";
}


// if we're already logged in, redirect away again.
if (logged_on) {
    $messages[] = gettext("You are already logged on.");
    define('redirect_url', $redirect_url);
    $_SESSION['messages'] = $messages;
    header("Location: " . redirect_url);
    exit;
}

$l = optional_param('username');
$p = optional_param('password');

if (!empty($l) && !empty($p)) {
    $ok = authenticate_account($l, md5($p));
    if ($ok) {
        $messages[] = gettext("You have been logged on.");
        define('redirect_url', $redirect_url);
        $_SESSION['messages'] = $messages;
        header("Location: " . redirect_url);
        exit;
    } else {
        $messages[] = gettext("Unrecognised username or password. The system could not log you on, or you may not have activated your account.");
    }
} else if (!empty($l) || !empty($p)) { // if ONLY one was entered, make the error message.
    $messages[] = gettext("Either the username or password were not specified. The system could not log you on.");
}

$body = gettext('Please log in');
templates_page_setup();
// display the form.
echo templates_page_draw( array(
                                      sitename,
                                      templates_draw(array(
                                                           'body' => $body,
                                                           'title' => gettext('Log On'),
                                                           'context' => 'contentholder'
                                                           )
                                                     )
                                      )
        );

?>