<?php

    //    ELGG weblog view page
    	global $CFG;

    // Run includes
        require_once(dirname(dirname(__FILE__))."/includes.php");
        
        run("profile:init");
        run("weblogs:init");
        run("friends:init");
        
        define("context", "weblog");
        templates_page_setup();
        
        $title = run("profile:display:name") . " :: ". gettext($CFG->owned_users_caption." blog");        

        $body = run("content:weblogs:view");
        $body .= run("weblogs:ownedusers:view");
        
        $body = templates_draw(array(
                        'context' => 'contentholder',
                        'title' => $title,
                        'body' => "<div id=\"view_friends_blogs\">" . $body . "</div>"
                    )
                    );
                    
        echo templates_page_draw( array(
                        $title, $body
                    )
                    );

?>