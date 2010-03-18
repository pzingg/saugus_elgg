<?php

// Returns the user_type of a particular user as specified in $parameter

global $user_type;

if (!isset($user_type[$parameter])) {
    $user_type[$parameter] = get_field('users','user_type','ident',$parameter);
}

$run_result = $user_type[$parameter];
        
?>