<?php

    // Save the user's editor choice
    $action = optional_param('action');
    $id = optional_param('id',0,PARAM_INT);
    $value = optional_param('visualeditor');

    if (logged_on && !empty($action) 
        && run("permissions:check",
               array("userdetails:change",$id))) {
        if (!empty($value) && in_array($value,array('yes','no'))) {

            // Get the current value, will also create an initial entry if not yet set
            $current = run('userdetails:editor', $id);
            if ($current == $value) {
                $messages[] .= gettext("Your editor preferences have been saved");
            } else {
                if (set_field('user_flags','value',$value,'flag','visualeditor','user_id',$id)) {
                    $messages[] .= gettext("Your editor preferences have been changed");
                } else {
                    $messages[] .= gettext("Your editor preferences could not be changed");
                }
            }
        }
    }

?>
