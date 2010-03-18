<?php

// If isset $parameter (string), returns true if spam

if (isset($parameter)) {
	
	//captcha check
	$run_result = captcha_process();  //defined in mod/captcha/lib.php
	
	if (!$run_result) {
	    if (!$spam = get_field('datalists','value','name','antispam')) {
	        $spam = "";
	    }
	    
	    $spam = str_replace("\r","",$spam);
	    $spam = explode("\n",$spam);
	    
	    foreach($spam as $regexp) {
	        if (strlen($regexp) > 0) {
	            if (substr($regexp,0,1) != "#") {
	                if (@preg_match("/" . trim($regexp) . "/i", $parameter)) {
	                    $run_result = true;
	                }
	            }
	        }
	    }
	}
    
//    if (!$run_result && @preg_match_all("|<a href[^>]+>.*</a>|U", $parameter, $matches)) {
//    	$comment_size = 0;
//    	foreach ($matches[0] as $link) $comment_size += strlen($link);
//    	if (($comment_size / strlen($parameter) * 100) > 50) {
//    		$run_result = true;
//    	}
//    	else if (sizeof($matches[0]) > 2) {
//    		if (($comment_size / strlen($parameter) * 100) > 30)
//    			$run_result = true;
//    	}
//    }
    
    if (!$run_result && (substr($parameter,0,4) == "http"))
    	$run_result = true;
    	
    if (!$run_result && (@preg_match("/<a><\/a>/i", $parameter)))  //catch fool bot that keeps posting empty links
		$run_result = true;
    
    
}

?>