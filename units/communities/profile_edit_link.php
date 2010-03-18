<?php

    global $page_owner;
    global $CFG;

    if (run("users:type:get",$page_owner) == 'community') {

        if (run("permissions:check", "uploadicons")) {
        
            global $page_owner;
            
            $uploadandEdit = gettext("Upload / edit site pictures for this community.");
            $run_result .= <<< END
            
            <p>
                <a href="{$CFG->wwwroot}_icons/?profile_id={$page_owner}">$uploadandEdit </a>
            </p>
            
END;
        
        }
        if (run("permissions:check", "userdetails:change")) {
        
            global $page_owner;
            
            $changeName = gettext("Change this community's name.");
            $run_result .= <<< END
            
            <p>
                <a href="{$CFG->wwwroot}_userdetails/?profile_id={$page_owner}">$changeName</a>
            </p>
            
END;
        
        }
    }

?>