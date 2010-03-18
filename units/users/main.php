<?php

    global $CFG;
    /*
    *    Users plug-in
    */

    // Load configuration
        require_once(dirname(__FILE__)."/conf.php");
    
    // Library functions
        require_once(dirname(__FILE__)."/library.php");
    
    // Actions to perform on initialisation
        $function['init'][] = path . "units/users/function_session_start.php";
        $function['init'][] = path . "units/users/function_session_actions.php";
        $function['init'][] = path . "units/users/function_default_access_levels.php";
        $function['init'][] = path . "units/users/function_define_ownership.php";

    // User details initialisation
        $function['userdetails:init'][] = path . "units/users/userdetails_actions.php";
                
    // Actions to perform when we log on
        $function['users:log_on'][] = path . "units/users/function_log_on.php";
        
    // Actions to perform when we log off
        $function['users:log_off'][] = path . "units/users/function_log_off.php";

    // Functions to turn a username into a user ID and vice versa
        $function['users:name_to_id'][] = path . "units/users/function_name_to_id.php";
        $function['users:id_to_name'][] = path . "units/users/function_id_to_name.php";
        
    // Userinfo box
        $function['users:infobox'][] = path . "units/users/user_info.php";
        
    // User count underneath the logon pane
        // $function['display:log_on_pane'][] = path . "units/users/current_user_info.php";
        $function['display:log_on_pane'][] = path . "units/users/function_number_of_users.php";    
        // $function['display:sidebar'][] = path . "units/users/current_user_info.php";
        $function['display:sidebar'][] = path . "units/users/function_number_of_users.php";    
        
    // Access level select
        $function['display:access_level_select'][] = path . "units/users/function_access_level_select.php";
        
    // Check access levels
        $function['users:access_level_check'][] = path . "units/users/function_access_level_check.php";
        
    // Obtain SQL "where" string for access levels
        $function['users:access_level_sql_where'][] = path . "units/users/function_access_level_sql_where.php";
        
    // Display a user's name, given a user ID
        $function['users:display:name'][] = path . "units/users/function_display_name.php";
        
    // User details edit screen
        $function['userdetails:edit'][] = path . "units/users/userdetails_edit.php";
                
    // Information for the home screen
        $function['content:mainindex'][] = path . "units/users/content_main_index.php";
        
    // Get user type
        $function['users:type:get'][] = path . "units/users/get_type.php";
        
    // Permissions checker
        $function['permissions:check'][] = path . "units/users/permissions_check.php";
        
    // Flag functions:
    // Check the value of a flag
        $function['users:flags:get'][] = path . "units/users/flag_get.php";
    // Set the value of a flag
        $function['users:flags:set'][] = path . "units/users/flag_set.php";
    // Remove a flag
        $function['users:flags:unset'][] = path . "units/users/flag_unset.php";
        
?>
