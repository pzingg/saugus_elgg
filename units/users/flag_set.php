<?php

// Flag functions: set
// Ben Werdmuller, Sept 05
    
/* Parameters:

[0] - name of the flag
[1] - user ID
[2] - value to set
    
*/
    
$userid = (int) $parameter[1];

// Unset the flag first
run("users:flags:unset",array($parameter[0], $userid));

// Then add data
$flag = new StdClass;
$flag->flag = $parameter[0];
$flag->user_id = $userid;
$flag->value = $parameter[2];
insert_record('user_flags',$flag);
        
?>