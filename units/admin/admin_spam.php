<?php
global $USER,$CFG;
// Users panel

if (logged_on && run("users:flags:get", array("admin", $USER->ident))) {
    
    $run_result .= "<p>" . gettext("Add regular expressions below, one per line, to block spam. For example, 'foo' will block all comments containing the word foo, (foo|bar) will block comments containing the word foo or bar.") . "</p>";
    $run_result .= "<p>" . gettext("Blank lines and lines starting with # will be ignored.") . "</p>";
        
    if ($spam = get_record('datalists','name','antispam')) {
            $spam = htmlspecialchars(stripslashes($spam->value), ENT_COMPAT, 'utf-8');
            $spam = str_replace("&amp;","&",$spam); //add & characters back in for valid regex
    } else {
        $spam = "";
    }
    
    $run_result .= "<form action=\"\" method=\"post\">";
    
    $run_result .= templates_draw(array(
                                        'context' => 'databox',
                                        'name' => gettext("Regular expressions"),
                                        'column1' => display_input_field(array("antispam",$spam,"longtext","antispam")),
                                        'column2' => "<input type=\"hidden\" name=\"action\" value=\"admin:antispam:save\" /><input type=\"submit\" value=\"" . gettext("Save") . "\" />"
                                        )
                                  );
    
    $run_result .= "</form>";
    
}

?>