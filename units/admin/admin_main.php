<?php
global $USER,$CFG;
// Main admin panel screen
    
// Site stats

if (logged_on && run("users:flags:get", array("admin", $USER->ident))) {
    
        $run_result .= "<h2>" . gettext("Site statistics") . "</h2>";
    
    // Number of users of each type
    if ($users = count_users()) {
        foreach($users as $user) {
            
            $run_result .= templates_draw(array(
                                                'context' => 'adminTable',
                                                'name' => "<h3>" . sprintf(gettext("Accounts of type '%s'"), $user->user_type) . "</h3> ",
                                                'column1' => "<p>" . $user->numusers . "</p> ",
                                                'column2' => "&nbsp;"
                                                )
                                          );
            
        }
    }
    
    // Number of weblog posts
    $weblog_posts = count_records('weblog_posts');
    $weblog_comments = count_records('weblog_comments');
    $weblog_posts_7days = count_records_select('weblog_posts',"posted > ?",array(time() - (86400 * 7)));
    $weblog_comments_7days = count_records_select('weblog_comments',"posted > ?",array(time() - (86400 * 7)));
    $run_result .= templates_draw(array(
                                        'context' => 'adminTable',
                                        'name' => "<h3>" . gettext("Weblog statistics") . "</h3> ",
                                        'column1' => "<h4>" . gettext("All-time:") . "</h4><p>" 
                                        . sprintf(gettext("%d weblog posts, %d comments"),$weblog_posts, $weblog_comments) 
                                        . "</p><h4>" . gettext("Last 7 days:") . "</h4><p>" 
                                        . sprintf(gettext("%d weblog posts, %d comments"),$weblog_posts_7days, $weblog_comments_7days) . "</p>",
                                        'column2' => "&nbsp;"
                                        )
                                  );
    
    // Number of files
    $files = get_record_sql('SELECT count(ident) AS numfiles,sum(size) AS totalsize FROM '.$CFG->prefix.'files');
    $files_7days = get_record_sql('SELECT count(ident) as numfiles, sum(size) AS totalsize FROM '.$CFG->prefix.'files WHERE time_uploaded > ?',array(time() - (86400 * 7)));
    $run_result .= templates_draw(array(
                                        'context' => 'adminTable',
                                        'name' => "<h3>" . gettext("File statistics") . "</h3> ",
                                        'column1' => "<h4>" . gettext("All-time:") . "</h4> <p>" . sprintf(gettext("%d files (%d bytes)"),$files->numfiles, $files->totalsize) 
                                        . "</p><h4>" . gettext("Last 7 days:") . "</h4><p>" . sprintf(gettext("%d files (%d bytes)"),$files_7days->numfiles, $files_7days->totalsize) . "</p>",
                                        'column2' => "&nbsp;"
                                        )
                                  );
    
        // DB size
        $totaldbsize = 0;
        if ($CFG->dbtype == 'mysql') {
            if ($dbsize = get_records_sql('SHOW TABLE STATUS')) {
                foreach($dbsize as $atable) {
                    // filter on prefix if we have it.
                    if (!empty($CFG->prefix) && strpos($atable->Name,$CFG->prefix) !== 0) {
                        continue;
                    }
                    $totaldbsize += intval($atable->Data_length) + intval($atable->Index_length);
                }
                $run_result .= templates_draw(array(
                                                    'context' => 'adminTable',
                                                    'name' => "<h3>" . gettext("Database statistics") . "</h3> ",
                                                    'column1' => "<h4>" . gettext("Total database size:") . "</h4> <p>" . sprintf(gettext("%d bytes"),$totaldbsize) . "</p>",
                                                    'column2' => "&nbsp;"
                                                    )
                                              );
            }
        }
    // Users online right now
    $run_result .= "<h2>" . gettext("Users online now") . "</h2>";
    $run_result .= "<p>" . gettext("The following users have an active session and have performed an action within the past 10 minutes.") . "</p>";
    
    if ($users = get_records_select('users',"code != ? AND last_action > ?",array('',time() - 600),'username ASC')) {
        $run_result .= templates_draw(array(
                                            'context' => 'adminTable',
                                            'name' => "<h3>" . gettext("Username") . "</h3>",
                                            'column1' => "<h3>" . gettext("Full name") . "</h3>",
                                            'column2' => "<h3>" . gettext("Email address") . "</h3>"
                                            )
                                      );
        foreach($users as $user) {
            $run_result .= run("admin:users:panel",$user);
        }
    } else {
        $users = array();
    }
    
    $run_result .= "<p>" . sprintf(gettext("%d users in total."),sizeof($users)) . "</p>";
    
}

?>