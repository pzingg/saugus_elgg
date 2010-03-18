<?php
global $USER;
// User administration extras
    
// Get the page owner of the userdetails we're messing with

global $page_owner;

// Only logged in admins can do this! For obvious reasons. Also, you may not perform
// these actions on yourself.

if (logged_on && run("users:flags:get", array("admin", $USER->ident)) && $page_owner != $USER->ident) {
            
    // Get user details
    $user_details = get_record('users','ident',$page_owner);
    
    if ($user_details->username != "news") {
        
        $run_result .= "<h3>" . gettext("Change username:") . "</h3>";
        $run_result .= templates_draw(array(
                                            'context' => 'databox',
                                            'name' => gettext("New username: "),
                                            'column1' => "<input type=\"text\" name=\"change_username\" value=\"".$user_details->username."\" />"
                                            )
                                      );
        
    }
    
    $run_result .= "<h3>" . gettext("Change file quota (in bytes):") . "</h3>";
    $run_result .= templates_draw(array(
                                        'context' => 'databox',
                                        'name' => gettext("New file quota: "),
                                        'column1' => "<input type=\"text\" name=\"change_filequota\" value=\"".$user_details->file_quota."\" />"
                                        )
                                  );
    $run_result .= "<h3>" . gettext("Change icon quota:") . "</h3>";
    $run_result .= templates_draw(array(
                                        'context' => 'databox',
                                        'name' => gettext("New icon quota: "),
                                        'column1' => "<input type=\"text\" name=\"change_iconquota\" value=\"".$user_details->icon_quota."\" />"
                                        )
                                  );
    
    if ($user_details->user_type == "person") {
        $run_result .= "<h3>" . gettext("User flags:") . "</h3>";
        // Is the user an administrator?
        if (run("users:flags:get", array("admin", $page_owner))) {
            $checkedyes = "checked = \"true\"";
            $checkedno = "";
        } else {
            $checkedyes = "";
            $checkedno = "checked = \"true\"";
        }
        $run_result .= templates_draw(array(
                                            'context' => 'databox',
                                            'name' => gettext("Site administrator: "),
                                            'column1' => "<input type=\"radio\" name=\"flag[admin]\" value=\"1\" $checkedyes />" 
                                            . gettext("Yes") . " " . "<input type=\"radio\" name=\"flag[admin]\" value=\"0\" $checkedno />" . gettext("No")
                                            )
                                      );
        
        // Is the user banned?
        if (run("users:flags:get", array("banned", $page_owner))) {
            $checkedyes = "checked = \"true\"";
            $checkedno = "";
        } else {
            $checkedyes = "";
            $checkedno = "checked = \"true\"";
        }
        $run_result .= templates_draw(array(
                                            'context' => 'databox',
                                            'name' => gettext("Banned: "),
                                            'column1' => "<input type=\"radio\" name=\"flag[banned]\" value=\"1\" $checkedyes />" .
                                            gettext("Yes") . " " . "<input type=\"radio\" name=\"flag[banned]\" value=\"0\" $checkedno />"  . gettext("No")
                                            )
                                      );
    }
    
    // Allow for user administration flags from other plugins
    $run_result .= run("admin:user:flags",$page_owner);
    
}

?>