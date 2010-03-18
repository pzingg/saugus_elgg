<?php

    // Run includes
        require_once(dirname(dirname(__FILE__))."/includes.php");
        templates_page_setup();

    // Draw page
        echo templates_page_draw( array(
                    sprintf(gettext("%s Usage Guidelines"),sitename),
                    templates_draw(array(
                                                    'body' => run("content:guidelines"),
                                                    'name' => sprintf(gettext("%s Usage Guidelines"),sitename),
                                                    'context' => 'contentholder'
                                                )
                                                )
            )
            );

?>