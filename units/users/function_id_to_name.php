<?php

    // Name table
    
        global $id_to_name_table;

    // Returns user's username from a given ID
    
        if (isset($parameter) && $parameter != "") {
            
            $parameter = (int) $parameter;
            if (!isset($id_to_name_table[$parameter])) {
                $id_to_name_table[$parameter] = get_field('users','username','ident',$parameter);
            }
            $run_result = $id_to_name_table[$parameter];
            
        }
        
?>