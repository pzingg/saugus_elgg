<?php
    
    // Run includes
        require_once(dirname(dirname(__FILE__))."/includes.php");
    
    define("context", "files");
    templates_page_setup();
    // Draw page
        echo templates_page_draw( array(
                    sprintf(gettext("Help for %s"),sitename),
                    templates_draw(array(
                                                    'body' => run("help:files"),
                                                    'title' => gettext("'Your Files' help"),
                                                    'context' => 'contentholder'
                                                )
                                                )
            )
            );
        
?>