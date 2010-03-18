<?php
    // $Id$

	include('config.php');
	require(captcha_inc . 'csi.php');

	// elgg session
	define('context','external');
	require_once(dirname(dirname(dirname(__FILE__))) . '/includes.php');

    // path to find font file
	putenv('GDFONTPATH=' . captcha_inc);

	// generate captcha image
	header('Content-Type: image/jpeg');
	//$img = new CaptchaSecurityImages(155,45,5);
	$img = new CaptchaSecurityImages(170,50,5);

?>
