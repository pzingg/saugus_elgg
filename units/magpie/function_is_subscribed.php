<?php

    $run_result = false;

    if (logged_on && isset($parameter)) {

        global $page_owner;
        $parameter = (int) $parameter;
        
        if ($page_owner == $_SESSION['userid']) {
        
            global $rss_subscriptions;
            
        run("rss:subscriptions:get");
        
        $run_result = in_array($parameter, $rss_subscriptions);

        } else if (run("permissions:check", "profile")) {
            
            $num = count_records('feed_subscriptions','user_id',$page_owner,'ident',$parameter);
            if (!empty($num)) {
                $run_result = true;
            }
            
        }

    }
        
?>