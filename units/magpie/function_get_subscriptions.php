<?php
global $USER;
if (logged_on) {
    
    global $rss_subscriptions;
    $parameter = (int) $parameter;
    
    if (!isset($rss_subscriptions)) {
        $rss_subscriptions = array();
        if ($subscriptions_var = get_records('feed_subscriptions','user_id',$USER->ident)) {
            foreach($subscriptions_var as $subscription) {
                $rss_subscriptions[] = $subscription->feed_id;
            }
        }
        
    }
    $run_result = $rss_subscriptions;
}

?>