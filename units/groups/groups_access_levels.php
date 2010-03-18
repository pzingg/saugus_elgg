<?php

// Get groups
    
if ($groups = run("groups:get",array($_SESSION['userid']))) {
    foreach($groups as $group) {
        
        $data['access'][] = array(gettext("Group") . ": " . $group->name, "group" . $group->ident);
        
    }
}

?>