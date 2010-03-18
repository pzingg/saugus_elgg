<?php

    require_once(dirname(dirname(__FILE__))."/includes.php");
    
    run("weblogs:init");
    run("profile:init");
    run("rss:init");
    
    define('context','resources');
    global $page_owner;
    templates_page_setup();    
    $title = run("profile:display:name") ." :: " . gettext("Feeds");
    
    run("rss:update:all",$page_owner);
    $body = run("rss:view",$page_owner);
    
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