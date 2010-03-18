<?php

    //    ELGG display popular tags page

    // Run includes
        require_once(dirname(dirname(__FILE__))."/includes.php");
        
        run("search:init");
        run("profile:init");
        templates_page_setup();        
        global $page_owner;
        
        $title = run("users:display:name", $page_owner) . " :: " . gettext("Tags");

        $body = run("content:tags");
        $body .= run("search:tags:personal:display");
        
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