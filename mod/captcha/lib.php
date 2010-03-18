<?php
// $Id$

//
// plugin functions
//
function captcha_init () {
    global $function;

    include_once(dirname(__FILE__) . '/config.php');

    if (defined('captcha_anon_only')) {
        $function['mod:captcha:display'][] = captcha_dir . 'function_captcha_display.php';
        

        // captcha for comments 
        //listen_for_event('weblog_comment','create','captcha_process');

        //TODO: captcha for register invite
        //listen_for_event('invite','register','captcha_process');
        
        //TODO: captcha for forgotten password
        //listen_for_event('password','recover','captcha_process');

    }
}

function captcha_pagesetup () {
	captcha_init();
}

// 
// captcha functions
//
function captcha_process () {
    global $messages;

    if (captcha_go()) {
        $post_code = trim(optional_param('security_code'));

        if (empty($post_code)) {
            $messages[] = gettext('Please enter the verification code.');
            return true;
        } else if (!is_human($post_code)) {
            $messages[] = gettext('Verification code incorrect. Please try again.');
            return true;
        } 
    }
     
    //$messages[] = gettext('Verification code passed.');
    return false;
}

// return true if security code matchs
function is_human ($post_code) {
    $sess_code = $_SESSION['security_code'];

    if (!empty($sess_code) && $sess_code === $post_code) {
        return true;
    } else {
        return false;
    }
}

// return true if have to process captcha
function captcha_go () {
	include_once(dirname(__FILE__) . '/config.php');
    $captcha_flag = defined('captcha_anon_only') &&
                    (!captcha_anon_only || !isloggedin());
    if ($captcha_flag) {
        //echo 'captcha flag true';
        return true;
    } else {
        //echo 'captcha flag true';
        return false;
    }
}

function captcha_print_form() {
    $Title = gettext('Please enter the code in the image below');

    return templates_draw(array(

                        'context' => 'databox1',
                        'name' => $Title,
                        'column1' => '<img src="' . url . captcha_url . 'pic.php" alt="Security Code" /><br /><input type="text" name="security_code" />'

                    )
                    );
}

?>
