<?php

    require_once(dirname(dirname(__FILE__))."/includes.php");
    
    global $page_owner;

    run("weblogs:init");
    run("profile:init");
    run("rss:init");
    
    define('context','resources');

    templates_page_setup();    

    $title = run("profile:display:name") ." :: " . gettext("Publish feeds to blog");
    
    $body = run("rss:subscriptions:publish:blog");
    
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