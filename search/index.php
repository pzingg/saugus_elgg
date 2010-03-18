<?php

    //    ELGG profile search page

    // Run includes
        require_once(dirname(dirname(__FILE__))."/includes.php");
        
        run("search:init");
        run("search:all:tagtypes");
        
        $title = gettext("Search");
        templates_page_setup();

        $body = run("content:profile:search");
        $body .= run("search:display");
        
        $body = templates_draw(array(
                        'context' => 'contentholder',
                        'title' => $title,
                        'body' => $body
                    )
                    );
                    
        echo templates_page_draw( array(
                    $title, $body
                )
                );

?>