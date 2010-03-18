<?php
global $USER;
global $CFG;
global $page_owner;
$profile_id = $page_owner;

if ($page_owner != -1 && $page_owner != $USER->ident) {
    $posts = count_records_select('files',"files_owner = $profile_id AND (".run("users:access_level_sql_where",$profile_id).")");
    
    if ($USER->ident == $profile_id) {
        $title = gettext("Your Files");
    } else {
        $title = gettext("Files");
    }
    
    if ($posts == 1) {
        $filesstring = $posts . " file";
    } else {
        $filesstring = $posts . " files";
    }
    
    $weblog_username = run("users:id_to_name",$profile_id);
    $fileStorage = gettext("File Storage"); // gettext variable
    $body = <<< END
        <ul>
            <li><a href="{$CFG->wwwroot}{$weblog_username}/files/">$fileStorage</a> ($filesstring)</li>
            <li>(<a href="{$CFG->wwwroot}{$weblog_username}/files/rss/">RSS</a>)</li>
        </ul>
END;

    $run_result .= "<li id=\"sidebar_files\">";
    $run_result .= templates_draw(array(
                                        'context' => 'sidebarholder',
                                        'title' => $title,
                                        'body' => $body
                                        )
                                  );
    $run_result .= "</li>";
}
        
?>