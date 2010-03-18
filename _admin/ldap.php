<?php

    //    Jim Klein's ELGG ldap server admin panel page

    // Run includes
        require_once(dirname(dirname(__FILE__))."/includes.php");
        
    // Initialise functions for user details, icon management and profile management
        run("admin:init");

        define("context", "admin");
        templates_page_setup();
        
    // You must be logged on to view this!
                                
        echo templates_page_draw( array(
                    gettext("Manage LDAP servers"),
                    templates_draw(array(
                        'context' => 'contentholder',
                        'title' => gettext("Manage LDAP servers"), 
                        'body' => run("admin:ldap")
                    )
                    )
                )
                );

?>