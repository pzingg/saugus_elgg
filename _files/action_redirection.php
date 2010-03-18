<?php

//    ELGG files perform-action-then-redirect page

// Run includes
require_once(dirname(dirname(__FILE__))."/includes.php");

run("files:init");

global $redirect_url;
global $messages;
global $page_owner;

$page_owner = optional_param('files_owner');

if (isset($messages) && sizeof($messages) > 0) {
    $_SESSION['messages'] = $messages;
}

if (defined('redirect_url')) {
    header("Location: " . redirect_url);
} else {
    header("Location: " . url);
}

?>