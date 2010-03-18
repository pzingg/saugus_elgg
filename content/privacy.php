<?php

    // Run includes
        define("context","external");
        require_once(dirname(dirname(__FILE__))."/includes.php");
        templates_page_setup();

    // Draw page
        echo templates_page_draw( array(
                    sprintf(gettext("%s Privacy Policy"),sitename),
                    templates_draw(array(
                                                    'body' => run("content:privacy"),
                                                    'name' => sprintf(gettext("%s Privacy Policy"), sitename),
                                                    'context' => 'contentholder'
                                                )
                                                )
            )
            );
        
?>
