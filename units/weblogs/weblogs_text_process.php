<?php

    // Processes text
    
    if (isset($parameter)) {
        if (is_array($parameter)) {
            $run_result .= nl2br(trim($parameter[0]));
            $addrelnofollow = (isset($parameter[1])) ? (bool) $parameter[1] : false;
        } else {
            $run_result .= nl2br(trim($parameter));
            $addrelnofollow = false;
        }
        
        
        // URLs to links
        
        $run_result = run("weblogs:html_activate_urls", $run_result);
        
        // Remove the evil font tag
        $run_result = preg_replace("/<font[^>]*>/i","",$run_result);
        $run_result = preg_replace("/<\/font>/i","",$run_result);
        
        // add rel="nofollow" to any links
        if ($addrelnofollow) {
            $run_result = preg_replace('/<a\s+([^>]*)\s+rel=/i', '<a $1 ', $run_result);
            $run_result = preg_replace('/<a\s+/i', '<a rel="nofollow" ', $run_result);
        }
        
        // Text cutting
        // Commented out for the moment as it seems to disproportionately increase
        // memory usage / load
        
        /*
        global $individual;
        
        if (!isset($individual) || $individual != 1) {
            $run_result = preg_replace("/\{\{cut\}\}(.|\n)*(\{\{uncut\}\})?/","{{more}}",$run_result);
        } else {
            // $run_result = preg_replace("/\{\{cut\}\}/","",$run_result);
            $run_result = str_replace("{{cut}}","",$run_result);
            $run_result = str_replace("{{uncut}}","",$run_result);
        }
        */
    }

?>