<?php

// Name table

global $name_to_id_table;

// Returns user's ID from a given name

if (isset($parameter) && $parameter != "") {
    
    if (!isset($name_to_id_table[$parameter])) {
        $name_to_id_table[$parameter] = get_field('users','ident','username',$parameter);
    }
    $run_result = $name_to_id_table[$parameter];
    
}
        
?>