<?php

    global $page_owner;

    $rss_username = run("users:id_to_name",$page_owner);
    
    if (context=="resources") {
    
        if ($page_owner != -1) {

                
                    $run_result .= templates_draw(array(
                                'context' => 'submenuitem',
                                'name' => gettext("Feeds"),
                                'location' =>  url . $rss_username . "/newsclient/"
                            )
                            );
                        

                    $run_result .= templates_draw(array(
                                        'context' => 'submenuitem',
                                        'name' => gettext("View aggregator"),
                                        'location' =>  url . $rss_username . "/newsclient/all/"
                                    )
                                    );
                                
            }

            $run_result .= templates_draw(array(
                            'context' => 'submenuitem',
                            'name' => gettext("Popular feeds"),
                            'location' =>  url . "_rss/popular.php"
                        )
                        );

             /* $run_result .= templates_draw(array(
                            'context' => 'submenuitem',
                            'name' => gettext("Page help"),
                            'location' => url . 'help/feeds_help.php'
                        )
                        ); */

                    
    }

?>