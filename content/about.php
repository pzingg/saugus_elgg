<?php
    
    // Run includes
        require_once(dirname(dirname(__FILE__))."/includes.php");
        templates_page_setup();

    // Draw page
        echo templates_page_draw( array(
                    sprintf(gettext("About %s"), sitename),
                    templates_draw(array(
                                                    'body' => run("content:about"),
                                                    'name' => sprintf(gettext("About %s"), sitename),
                                                    'context' => 'contentholder'
                                                )
                                                )
            )
            );
        
?>