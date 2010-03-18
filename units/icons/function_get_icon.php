<?php

    // TODO: This should almost certainly be a function rather than a run() command

    if (!empty($parameter)) {
        
        global $icon_cache;
        $user_id = (int) $parameter;
        
        if (!isset($icon_cache[$user_id]) || (time() - $icon_cache[$user_id]->created > 60)) {
    
            $icon_cache[$user_id]->created = time();
            $icon_cache[$user_id]->data = get_field('users','icon','ident',$user_id);
            
        }
        $run_result = $icon_cache[$user_id]->data;
        
    }

?>