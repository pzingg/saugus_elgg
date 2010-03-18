<?php

global $page_owner;
if ((!logged_on) && $page_owner == -1) {
    
    $result = count_users('person');
    $result = "<p>" . sprintf(gettext("There are %d active users."),$result);
    $body = $result;
    $body .= "<br />";
    
    $result = count_users('person',time() - 600);
    $body .= sprintf(gettext("(%d logged on.)"), $result) . "</p>";
    $run_result .= "<li>";       
    $run_result .= templates_draw(array(
                                        'template' => -1,
                                        'context' => 'sidebarholder',
                                        'title' => gettext("User Statistics"),
                                        'body' => $body,
                                        'submenu' => ''
                                        )
                                  );
    $run_result .= "</li>";
}

?>