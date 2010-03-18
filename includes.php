<?php
	
    // error_reporting(E_ERROR | E_WARNING | E_PARSE);

    // All installation specific parameters should be in a file 
    // that is not part of the standard distribution.
        if (!file_exists(dirname(__FILE__)."/config.php")) {
            die('Elgg configuration error: config.php is missing. Please see INSTALL file.');
        }
        require_once(dirname(__FILE__)."/config.php");

    // Check for .htaccess
        if (!file_exists(dirname(__FILE__)."/.htaccess")) {
            die('Elgg configuration error: .htaccess is missing. Please see INSTALL file.');
        }
        
    // Check config values make sense
        require_once(dirname(__FILE__).'/sanitychecks.php');

    // Load datalib
        require_once($CFG->dirroot.'lib/datalib.php');

    // Load elgglib
        require_once($CFG->dirroot.'lib/elgglib.php');
        
    // Load constants
        require_once($CFG->dirroot.'lib/constants.php');

    // Load setup.php which will initialize database connections and such like.
        require_once($CFG->dirroot.'lib/setup.php');

    // Load required system files: do not edit this line.
        require_once(dirname(__FILE__)."/includes_system.php");

    
        
    // Check database
        require_once($CFG->dirroot.'lib/dbsetup.php');

    /***************************************************************************
    *    INSERT PLUGINS HERE
    *    Eventually this should be replaced with plugin autodiscovery
    ****************************************************************************/
        require($CFG->dirroot . "units/ldap/main.php");
        
    // XMLRPC
    //    @include($CFG->dirroot . "units/rpc/main.php");
    
    /***************************************************************************
    *    CONTENT MODULES
    *    This should make languages easier, although some kind of
    *    selection process will be required
    ****************************************************************************/
        
    // General
        include_once($CFG->dirroot . "content/general/main.php");
    // Main index
        include_once($CFG->dirroot . "content/mainindex/main.php");
    // User-related
        include_once($CFG->dirroot . "content/users/main.php");
    
    /***************************************************************************
    *    HELP MODULES
    ****************************************************************************/
        
    // Include main
        include_once($CFG->dirroot . "help/mainindex/main.php");
        
    // Visual editor (tinyMCE)
        @include($CFG->dirroot . "units/tinymce/main.php");
    
    // Calendaring system
    //    require($CFG->dirroot . "units/calendar/main.php");
    
    /***************************************************************************
    *    START-OF-PAGE RUNNING
    ****************************************************************************/
    
        run("init");
        
    // Walled garden checking: if we're not logged in,
    // and walled garden functionality is turned on, redirect to
    // the logon screen
        if (!empty($CFG->walledgarden) && context != "login" && !logged_on) {
            
            header("Location: " . $CFG->wwwroot . "login/index.php");
            exit();
            
        }
        
?>