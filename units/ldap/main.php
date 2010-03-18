<?php

    // Elgg ldap administration utilities
    // Jim Klein, Nov 2006

    // These utilities allow users tagged with the 'administration' flag
    // to manage ldap logon servers
    
    
    // Menu to view all ldap servers
        $function['admin:ldap'][] = path . "units/ldap/admin_ldap.php";
        
    // ldap server addition screen
        $function['admin:ldap:add'][] = path . "units/ldap/admin_ldap_add.php";
        
    // ldap related actions
        $function['init'][] = path . "units/ldap/ldap_actions.php";
    
    // ldap server list underneath the logon pane - doesn't work because elgg is appears to only be using display:sidebar
        $function['display:log_on_pane'][] = path . "units/ldap/function_list_servers.php"; 

?>