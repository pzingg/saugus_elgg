<?php

global $page_owner;
global $CFG;
$profile_id = $page_owner;
$sitename = sitename;

if (logged_on && $page_owner == $_SESSION['userid']) {
    
    $title = gettext("Recent Activity");
    $body = "<ul><li>"; 
    $body .= "<a href=\"".url."_activity/\">".gettext("View your activity") . "</a></li></ul>";

            $run_result .= "<li id=\"recent_activity\">";
    $run_result .= templates_draw(array(
                                        'context' => 'sidebarholder',
                                        'title' => $title,
                                        'body' => $body,
                                        )
                                  );
            $run_result .= "</li>";

} else {

    $posts = count_records_select('weblog_posts','('.run("users:access_level_sql_where",$profile_id).") and owner = $profile_id");
    
    if (logged_on || (isset($page_owner) && $page_owner != -1)) {
        
        $title = gettext("Blog");
        
        $weblog_username = run("users:id_to_name",$profile_id);
        $body = <<< END
            <ul>
END;
        if (run("users:type:get",$page_owner) == "person") {
            $personalWeblog = gettext("Personal blog");
            $body .= <<< END
                <li><a href="{$CFG->wwwroot}{$weblog_username}/weblog/">$personalWeblog</a> (<a href="{$CFG->wwwroot}{$weblog_username}/weblog/rss">RSS</a>)</li>
END;
        } else if (run("users:type:get",$page_owner) == "community") {
            $communityWeblog = gettext("Community blog");
            $body .= <<< END
                <li><a href="{$CFG->wwwroot}{$weblog_username}/weblog/">$communityWeblog</a> (<a href="{$CFG->wwwroot}{$weblog_username}/weblog/rss">RSS</a>)</li>
END;
        }
        $blogArchive = gettext("Weblog Archive");
        $friendWeblog = gettext("Friends blog");
        $body .= <<< END
                <li><a href="{$CFG->wwwroot}{$weblog_username}/weblog/archive/">$blogArchive</a></li>
                <li><a href="{$CFG->wwwroot}{$weblog_username}/weblog/friends/">$friendWeblog</a></li>
END;
        
        $run_result .= "<li id=\"sidebar_weblog\">";
        $run_result .= templates_draw(array(
                                            'context' => 'sidebarholder',
                                            'title' => $title,
                                            'body' => $body,
                                            )
                                      );
        $run_result .= "</ul></li>";
    }
}

?>