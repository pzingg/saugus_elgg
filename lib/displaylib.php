<?php 

// Function to sanitise RTF edit text
/* function RTESafe($strText) {
    //returns safe code for preloading in the RTE
    $tmpString = trim($strText);
    
    //convert all types of single quotes
    $tmpString = str_replace(chr(145), chr(39), $tmpString);
    $tmpString = str_replace(chr(146), chr(39), $tmpString);
    $tmpString = str_replace("'", "&#39;", $tmpString);
    
    //convert all types of double quotes
    $tmpString = str_replace(chr(147), chr(34), $tmpString);
    $tmpString = str_replace(chr(148), chr(34), $tmpString);
    
    //replace carriage returns & line feeds
    $tmpString = str_replace(chr(10), " ", $tmpString);
    $tmpString = str_replace(chr(13), " ", $tmpString);
    
    return $tmpString;
} */

function display_input_field ($parameter) {
    // Displays different HTML depending on input field type

    /*
    
    $parameter(
        
                        0 => input name to display (for forms etc)
                        1 => data
                        2 => type of input field
                        3 => reference name (for tag fields and so on)
                        4 => ID number (if any)
                        5 => Owner
        
                    )
    
    */

    $run_result = '';

    if (isset($parameter) && sizeof($parameter) > 2) {
            
        if (!isset($parameter[4])) {
            $parameter[4] = -1;
        }
            
        if (!isset($parameter[5])) {
            $parameter[5] = $_SESSION['userid'];
        }
            
        $cleanid = $parameter[0];
        if (!ereg("^[A-Za-z][A-Za-z0-9_:\\.-]*$", $cleanid)) {
            if (!ereg("^[A-Za-z]", $cleanid)) {
                $cleanid = "id_" . $cleanid;
            }
            $cleanid = ereg_replace("[^A-Za-z0-9_:\\.-]", "__", $cleanid);
        }
         
        switch($parameter[2]) {
                
        case "text":
            $run_result .= "<input type=\"text\" name=\"".$parameter[0]."\" value=\"".htmlspecialchars(stripslashes($parameter[1]), ENT_COMPAT, 'utf-8')."\" style=\"width: 95%\" id=\"".$cleanid."\" />";
            break;
        case "password":
            $run_result .= "<input type=\"password\" name=\"".$parameter[0]."\" value=\"".htmlspecialchars(stripslashes($parameter[1]), ENT_COMPAT, 'utf-8')."\" style=\"width: 95%\" id=\"".$cleanid."\" />";
            break;
        case "mediumtext":
            $run_result .= "<textarea name=\"".$parameter[0]."\" id=\"".$cleanid."\" style=\"width: 95%; height: 100px\">".htmlspecialchars(stripslashes($parameter[1]), ENT_COMPAT, 'utf-8')."</textarea>";
            break;
        case "keywords":
            /*
                        $keywords = stripslashes($parameter[1]);
                        preg_match_all("/\[\[([A-Za-z0-9 ]+)\]\]/i",$keywords,$keyword_list);
                        $keyword_list = $keyword_list[1];
                        $keywords = "";
                        if (is_array($keyword_list) && sizeof($keyword_list) > 0) {
                            sort($keyword_list);
                            foreach($keyword_list as $key => $list_item) {
                                $keywords .= $list_item;
                                if ($key < sizeof($keyword_list) - 1) {
                                    $keywords .= ", ";
                                }
                            }
                        }
                        $parameter[1] = $keywords;
            */
            if (!isset($data['profile:preload'][$parameter[3]])) {
                $keywords = "";
                if ($tags = get_records_select('tags',"tagtype = ? and ref = ? and owner = ?",array($parameter[3],$parameter[4],$parameter[5]),'tag ASC')) {
                    $first = true;
                    foreach($tags as $key => $tag) {
                        if (empty($first)) {
                            $keywords .= ", ";
                        }
                        $keywords .= stripslashes($tag->tag);
                        $first = false;
                    }
                }
                $parameter[1] = $keywords;
            } else {
                // $parameter[1] = $data['profile:preload'][$parameter[3]];
            }
            // $parameter[1] = var_export($parameter,true);
            $run_result .= "<textarea name=\"".$parameter[0]."\" id=\"".$cleanid."\" style=\"width: 95%; height: 100px\">".htmlspecialchars(stripslashes($parameter[1]), ENT_COMPAT, 'utf-8')."</textarea>";
            break;
        case "longtext":
            $run_result .= "<textarea name=\"".$parameter[0]."\" id=\"".$cleanid."\" style=\"width: 95%; height: 200px\">".htmlspecialchars(stripslashes($parameter[1]), ENT_COMPAT, 'utf-8')."</textarea>";
            break;
        case "richtext":
            // Rich text editor:
            $run_result .= <<< END
                <script language="JavaScript" type="text/javascript">
                <!--
                function submitForm() {
                    //make sure hidden and iframe values are in sync before submitting form
                    //to sync only 1 rte, use updateRTE(rte)
                    //to sync all rtes, use updateRTEs
                    updateRTE('<?php echo $parameter[0]; ?>');
                    //updateRTEs();
                    //alert("rte1 = " + document.elggform.<?php echo $parameter[0]; ?>.value);
                                
                    //change the following line to true to submit form
                    return true;
                }
END;
            $content = RTESafe(stripslashes($parameter[1]));
            $run_result .= <<< END
                //Usage: initRTE(imagesPath, includesPath, cssFile)
                initRTE("/units/display/rtfedit/images/", "/units/display/rtfedit/", "/units/display/rtfedit/rte.css");
            </script>
                  <noscript><p><b>Javascript must be enabled to use this form.</b></p></noscript>
                  <script language="JavaScript" type="text/javascript">
                  <!--
                  writeRichText('<?php echo $parameter[0];?>', '<?php echo $content; ?>', 220, 200, true, false);
            // -->
            </script>
END;
            break;
        case "blank":
            $run_result .= "<input type=\"hidden\" name=\"".$parameter[0]."\" value=\"blank\" id=\"".$cleanid."\" />";
            break;
        case "web":
        case "email":
        case "aim":
        case "msn":
        case "skype":
        case "icq":
            $run_result .= "<input type=\"text\" name=\"".$parameter[0]."\" value=\"".htmlspecialchars(stripslashes($parameter[1]), ENT_COMPAT, 'utf-8')."\" style=\"width: 95%\" id=\"".$cleanid."\" />";
            break;
        case "weblogtext":
            $run_result .= "<textarea name=\"".$parameter[0]."\" id=\"".$parameter[0]."\" style=\"width: 95%; height: 200px\">".htmlspecialchars(stripslashes($parameter[1]), ENT_COMPAT, 'utf-8')."</textarea>";
            break;
        }
            
    }
    
    return $run_result;
}

function log_on_pane () {


    // Elgg default globals
    global $function;
    global $log;
    global $actionlog;
    global $errorlog;
    global $messages;
    global $data;

    global $page_owner;
    
    global $CFG;
        
    // If this is someone else's portfolio, display the user's icon
    if ($page_owner != -1) {
        $run_result .= run("profile:user:info");
    }

    if ((!defined("logged_on") || logged_on == 0) && $page_owner == -1) {

        $body = '<form action="'.url.'login/index.php" method="post">';

        if (public_reg == true && ($CFG->maxusers == 0 || (count_users('person') < $CFG->maxusers))) {
            $reg_link = '<a href="' . url . '_invite/register.php">'. gettext("Register") .'</a> |';
        } else {
            $reg_link = "";
        }

        $body .= templates_draw(array(
                                      'template' => -1,
                                      'context' => 'contentholder',
                                      'title' => gettext("Log On"),
                                      'submenu' => '',
                                      'body' => '
            <table>
                <tr>
                    <td align="right"><p>
                        <label>' . gettext("Username") . '&nbsp;<input type="text" name="username" id="username" style="size: 200px" /></label><br />
                        <label>' . gettext("Password") . '&nbsp;<input type="password" name="password" id="password" style="size: 200px" />
                        </label></p>
                    </td>
                </tr>
                <tr>
                    <td align="right"><p>
                        <input type="hidden" name="action" value="log_on" />
                        <label>' . gettext("Log on") . ':<input type="submit" name="submit" value="'.gettext("Go").'" /></label><br /><br />
                        <label><input type="checkbox" name="remember" checked="checked" />
                                ' . gettext("Remember Login") . '</label><br />
                        <small>
                            ' . $reg_link . '
                            <a href="' . url . '_invite/forgotten_password.php">'. gettext("Forgotten password") .'</a>
                        </small></p>
                    </td>
                </tr>
            
            </table>

'
                                      )
                                );
        $body .= "</form>";

        $run_result .= $body;
            
    }

    return $run_result;
}

function display_output_field ($parameter) {
    // Displays different HTML depending on input field type

    /*
    
    $parameter(
        
                        0 => input name to display (for forms etc)
                        1 => data
                        2 => type of input field
                        3 => reference name (for tag fields and so on)
                        4 => ID number (if any)
                        5 => Owner (if not specified, current $page_owner is assumed)
        
                    )
    
    */
    
    global $db;
    global $page_owner;

    $run_result = '';
    
    if (isset($parameter) && sizeof($parameter) > 1) {
            
        if (!isset($parameter[4])) {
            $parameter[4] = -1;
        }
        if (!isset($parameter[5])) {
            if (isset($page_owner)) {
                $parameter[5] = $page_owner;
            } else {
                $parameter[5] = -1;
            }
        }
            
        switch($parameter[1]) {
                
        case "icq":
            $run_result = "<img src=\"http://web.icq.com/whitepages/online?icq=".htmlspecialchars(stripslashes($parameter[0]), ENT_COMPAT, 'utf-8')."&amp;img=5\" height=\"18\" width=\"18\" />  <b>".htmlspecialchars(stripslashes($parameter[0]), ENT_COMPAT, 'utf-8')."</b> (<a href=\"http://wwp.icq.com/scripts/search.dll?to=".htmlspecialchars(stripslashes($parameter[0]), ENT_COMPAT, 'utf-8')."\">" . gettext("Add User") . "</a>, <a href=\"http://wwp.icq.com/scripts/contact.dll?msgto=".htmlspecialchars(stripslashes($parameter[0]), ENT_COMPAT, 'utf-8')."\">". gettext("Send Message") ."</a>)";
            break;
        case "skype":
            $run_result = "<a href=\"callto://".htmlspecialchars(stripslashes($parameter[0]), ENT_COMPAT, 'utf-8')."\">".htmlspecialchars(stripslashes($parameter[0]), ENT_COMPAT, 'utf-8')."</a> <img src=\"http://goodies.skype.com/graphics/skypeme_btn_small_white.gif\" alt=\"Skype Me!\" border=\"0\" />";
            break;
        case "msn":
            $run_result = "MSN <b>".htmlspecialchars(stripslashes($parameter[0]), ENT_COMPAT, 'utf-8')."</b>";
            break;
        case "aim":
            $run_result = "<img src=\"http://big.oscar.aol.com/".htmlspecialchars(stripslashes($parameter[0]), ENT_COMPAT, 'utf-8')."?on_url=http://www.aol.com/aim/gr/online.gif&amp;off_url=http://www.aol.com/aim/gr/offline.gif\" width=\"14\" height=\"17\" /> <b>".htmlspecialchars(stripslashes($parameter[0]), ENT_COMPAT, 'utf-8')."</b> (<a href=\"aim:addbuddy?screenname=".htmlspecialchars(stripslashes($parameter[0]), ENT_COMPAT, 'utf-8')."\">". gettext("Add Buddy") ."</a>, <a href=\"aim:goim?screenname=".htmlspecialchars(stripslashes($parameter[0]), ENT_COMPAT, 'utf-8')."&amp;message=Hello\">". gettext("Send Message") ."</a>)";
            break;
        case "text":
        case "mediumtext":
        case "longtext":
            $run_result = nl2br(stripslashes($parameter[0]));
            break;
        case "keywords":
            /* 
            $keywords = stripslashes($parameter[0]);
            preg_match_all("/\[\[([A-Za-z0-9 ]+)\]\]/i",$keywords,$keyword_list);
            $keyword_list = $keyword_list[1];
            $keywords = "";
            if (is_array($keyword_list) && sizeof($keyword_list) > 0) {
                sort($keyword_list);
                $where = run("users:access_level_sql_where",$_SESSION['userid']);
                foreach($keyword_list as $key => $list_item) {
                    $number = count_records_select('profile_data',"($where) and name = '".$parameter[2]."' and value like \"%[[".$list_item."]]%\"");
                    if ($number > 1) {
                        $keywords .= "<a href=\"/profile/search.php?".$parameter[2]."=".$list_item."\" title=\"$number users\">";
                    }
                    $keywords .= $list_item;
                    if ($number > 1) {
                        $keywords .= "</a>";
                    }
                    if ($key < sizeof($keyword_list) - 1) {
                        $keywords .= ", ";
                    }
                }
            }
            $run_result = $keywords; 
            */
            $where = run("users:access_level_sql_where",$_SESSION['userid']);
            $keywords = "";
            if ($tags = get_records_select('tags',"($where) AND tagtype = " . $db->qstr($parameter[2]) . " AND ref = ".$parameter[4],null,'tag ASC')) {
                $first = true;
                foreach($tags as $tag) {
                    if (empty($first)) {
                        $keywords .= ", ";
                    }
                    $numberoftags = count_records('tags','tag',$tag->tag);
                    if ($numberoftags > 1) {
                        $keywords .= "<a href=\"".url."search/index.php?".$parameter[2]."=".urlencode(stripslashes($tag->tag))."&amp;ref=".$parameter[4]."&amp;owner=".$parameter[5]."\" >";
                    }
                    $keywords .= htmlspecialchars(stripslashes($tag->tag), ENT_COMPAT, 'utf-8');
                    if ($numberoftags > 1) {
                        $keywords .= "</a>";
                    }
                    $first = false;
                }
            }
            $run_result = $keywords;
            break;
        case "email":
            $run_result = preg_replace("/[\\d\\w\\.\\-_]+@([\\d\\w\\-_\\.]+\\.)+([\\w]{2,6})/i","<a href=\"mailto:$0\">$0</a>",$parameter[0]);
            break;
        case "web":
            $run_result = $parameter[0];
            if (substr_count($run_result,"http://") == 0) {
                $run_result = "http://" . $run_result;
            }
            $run_result = "<a href=\"" . $run_result . "\" target=\"_blank\">" . $run_result . "</a>";
            break;
        }
            
    }
    return $run_result;
}

function displaymenu_top () {

    global $PAGE;

    if (logged_on == 1) {
        
        return templates_draw(array(
                                    'context' => 'topmenu',
                                    'menuitems' => menu_join('', $PAGE->menu_top)
                                    )
                              );
        
    }

    return '';
}


function displaymenu () {

    global $PAGE;

    return templates_draw(array(
                                'context' => 'menu',
                                'menuitems' => menu_join('', $PAGE->menu)
                                )
                          );

}


function displaymenu_sub () {

    global $PAGE;

    if (logged_on == 1) {
        
        return templates_draw(array(
                                    'context' => 'submenu',
                                    'menuitems' => menu_join('', $PAGE->menu_sub)
                                    )
                              );
        
    }

    return '';
}

function displaymenu_user () {

    if (logged_on == 1) {
        
        return templates_draw(array(
                                    'context' => 'menu',
                                    'menuitems' => menu_join('', $PAGE->menu_user)
                                    )
                              );
        
    }

    return '';
}

function main () {


    // Elgg default globals
    global $function;
    global $log;
    global $actionlog;
    global $errorlog;
    global $messages;
    global $data;

            
    // Log on pane
    $function['display:log_on_pane'][] = path . "units/display/function_log_on_pane.php";
    $function['display:sidebar'][] = path . "units/display/function_log_on_pane.php";

    // Form elements
    $function['display:input_field'][] = path . "units/display/function_input_field_display.php";
    $function['display:output_field'][] = path . "units/display/function_output_field_display.php";

    return $run_result;
}



?>