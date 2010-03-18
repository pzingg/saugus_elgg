<?php

// Flag functions: get
// Ben Werdmuller, Sept 05

/* 
Parameters:
[0] - name of the flag
[1] - user ID
    
Returns:
        
$value - if the flag is set
false - if it isn't        
*/
    
$flagname = $parameter[0];
$userid = (int)$parameter[1];

if ($result = get_record('user_flags','flag',$flagname,'user_id',$userid)) {
    $run_result = stripslashes($result->value);
} else {
    $run_result = false;
}

?>