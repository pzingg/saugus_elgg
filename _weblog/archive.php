<?php

    //    ELGG weblog view page

    // Run includes
        require_once(dirname(dirname(__FILE__))."/includes.php");
        
        run("profile:init");
        run("weblogs:init");
        run("friends:init");
        
        define("context", "weblog");
        templates_page_setup();
        
        $title = run("profile:display:name") . " :: " . gettext("Blog Archives");
        
        $body = run("content:weblogs:archives:view");
        $body .= run("weblogs:archives:view");
        
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