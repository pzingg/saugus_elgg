<?php

// List of users in the system
    
if (logged_on && run("users:flags:get", array("admin", $_SESSION['userid']))) {
    
    $run_result .= "<p>" . gettext("The following is a list of LDAP servers that are available for authentication. You can click each one to edit its details.") . "</p>";
    
    if ($users = get_records('users','user_type','person','username ASC','*',0,50)) {
        $run_result .= templates_draw(array(
                                            'context' => 'adminTable',
                                            'name' => "<h5>" . gettext("Username") . "</h5>",
                                            'column1' => "<h5>" . gettext("Full name") . "</h5>",
                                            'column2' => "<h5>" . gettext("Email address") . "</h5>"
                                            )
                                      );
        foreach($users as $user) {
            $run_result .= run("admin:users:panel",$user);
        }

        
        $run_result .= templates_draw(array(
                                            'context' => 'adminTable',
                                            'name' => "&nbsp;",
                                            'column1' => $prev . "&nbsp;" . $next,
                                            'column2' => "&nbsp;"
                                            )
                                      );
        
    }
}

?>