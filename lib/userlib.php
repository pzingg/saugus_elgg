<?php

/**
 * Library of functions for user polling and manipulation.
 * Largely taken from the old /units/users/
 * Copyright (C) 2004-2006 Ben Werdmuller and David Tosh
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

 
// INITIALISATION //////////////////////////////////////////////////////////////

    // TODO: These need somewhere else to live. They're to do with
    // authentication and session management, not user management.

    // Session variable name
    define('user_session_name', 'elgguser');
    
    // Persistent login cookie DEFs
    define('AUTH_COOKIE', 'elggperm');
    define('AUTH_COOKIE_LENGTH', 31556926); // 1YR in seconds
    
    // Messages
    define('AUTH_MSG_OK', gettext("You have been logged on."));
    define('AUTH_MSG_BADLOGIN', gettext("Unrecognised username or password. The system could not log you on, or you may not have activated your account."));
    define('AUTH_MSG_MISSING', gettext("Either the username or password were not specified. The system could not log you on."));

// USER INFORMATION RETRIEVAL //////////////////////////////////////////////////

    // Given a user ID number, returns the specified field
    // Returns false if the user doesn't exist.
    function user_info($fieldname, $user_id) {
        
        // Name table
        static $id_to_name_table;

        // Returns field from a given ID

        if (!empty($user_id)) {
            $user_id = (int) $user_id;
            if (!isset($id_to_name_table[$user_id])) {
                $id_to_name_table[$user_id] = get_field('users',$fieldname,'ident',$user_id);
            }
            return $id_to_name_table[$user_id];
        }
        
        // If we've got here, the user didn't exist in the database
        return false;
        
    }
    
    // Given a username, returns the specified field
    // Returns false if the user doesn't exist.
    function user_info_username($fieldname, $username) {
        
        // Name table
        static $name_to_id_table;

        // Returns user's ID from a given name
        
        if (!empty($username)) {
            if (!isset($name_to_id_table[$username])) {
                $name_to_id_table[$username] = get_field('users',$fieldname,'username',$username);
            }
            return $name_to_id_table[$username];            
        }
        
        // If we've got here, the user didn't exist in the database
        return false;
        
    }
    
    // Gets the type of a particular user
    function user_type($user_id) {
        
        // Type table
        static $user_type_table;
        
        if (!isset($user_type_table[$user_id])) {
            $user_type_table[$user_id] = get_field('users','user_type','ident',$user_id);
        }
        
        return $user_type_table[$user_id];
    }
    
// USER FLAGS //////////////////////////////////////////////////////////////////

    // Gets the value of a flag
    function user_flag_get($flag_name, $user_id) {
        if ($result = get_record('user_flags','flag',$flag_name,'user_id',$user_id)) {
            return $result->value;
        }
        return false;
    }
    
    // Removes a flag
    function user_flag_unset($flag_name, $user_id) {
        return delete_records('user_flags','flag',$flag_name,'user_id',$user_id);
    }
    
    // Adds a flag
    function user_flag_set($flag_name, $value, $user_id) {
        // Unset the flag first
        user_flag_unset($flag_name, $user_id);

        // Then add data
        $flag = new StdClass;
        $flag->flag = $flag_name;
        $flag->user_id = $user_id;
        $flag->value = $value;
        return insert_record('user_flags',$flag);
    }
    
// ACCESS RESTRICTIONS /////////////////////////////////////////////////////////

    // Get current access level
    // Utterly deprecated (user levels no longer work like this), but kept 
    // alive for now.
    function accesslevel($owner = -1) {
        $currentaccess = 0;

        // For now, there are three access levels: 0 (logged out), 1 (logged in) and 1000 (me)
        if (logged_on == 1) {
            $currentaccess++;
        }
            
        if ($_SESSION['userid'] == $owner) {
            $currentaccess += 1000;
        }
            
        return $currentaccess;
    }
    
    // Protect users to a certain access level
    function protect($level, $owner = -1) {
        if (accesslevel($owner) < $level) {
            run("access_denied");
            exit();
        }
    }
    
// STATISTICS //////////////////////////////////////////////////////////////////

    // Count number of users
    // Optional: the user_type (eg 'person') and the minimum last time they
    // performed an action
    
    function count_users($type = '', $last_action = 0) {
        
        global $CFG;
        
        $where = "1 = 1";
        if (!empty($type)) {
            $where .= " AND user_type = '$type'";
        }
        if ($last_action > 0) {
            $where .= " AND last_action > " . $last_action;
        }
        if ($users = get_records_sql('SELECT user_type, count(ident) AS numusers 
                                  FROM '.$CFG->prefix.'users
                                  WHERE '.$where.'
                                  GROUP BY user_type')) {
            if (sizeof($users) > 1) {
                return $users;
            }
            foreach($users as $user) {
                return $user->numusers;
            }
        }
        
        return false;
    }

?>