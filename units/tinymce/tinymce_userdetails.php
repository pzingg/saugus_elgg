<?php

    // Get the user preferences for the editor

    // Userid
    $id = (int) $parameter;

    // Editor is enabeled by default
    $value = "yes";

    // Query result
    if ($result = get_field('user_flags','value','flag','visualeditor','user_id',$id)) {
        $value = $result;
    } else {
        // No result, store a default value
        $uf = new StdClass;
        $uf->flag = 'visualeditor';
        $uf->value = $value;
        $uf->user_id = $id;
        insert_record('user_flags',$uf);
    }

    $run_result = $value;
?>
