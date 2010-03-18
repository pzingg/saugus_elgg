<?php

    global $CFG;

    $portfolio = $CFG->wwwroot . $_SESSION['username'];
    $viewProfile = gettext("View your profile!"); // gettext variable
    $viewProfile2 = gettext("Your portfolio is the main way people find out about you. You can"); // gettext variable
    $viewProfile3 = gettext("edit your details"); // gettext variable
    $viewProfile4 = gettext("and choose exactly what you want to share with whom."); // gettext variable

    $run_result .= <<< END
    
        <p>
            <b>
                <a href="$portfolio">$viewProfile</a>
                $viewProfile2
                <a href="{$CFG->wwwroot}profile/edit.php">$viewProfile3</a> $viewProfile4
            </b>
        </p>
    
END;

if ($news = get_record_sql("SELECT wp.* FROM ".$CFG->prefix."weblog_posts wp
                            JOIN ".$CFG->prefix."users u ON u.ident = wp.weblog
                            WHERE u.username = ? ORDER BY posted DESC LIMIT 1",array('news'),false)) {
    $run_result .= "<div class=\"sitenews\">";
    $run_result .= "<h2>" . gettext("Latest news") . "</h2>";
    $run_result .= "<p>" . run("weblogs:text:process",nl2br(stripslashes($news->body))) . "</p>";
    $run_result .= "</div>";
}

?>