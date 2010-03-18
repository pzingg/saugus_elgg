<?php
global $CFG;
global $db;
    // Search criteria are passed in $parameter from run("search:display")
    
        $handle = 0;
        foreach($data['profile:details'] as $profiletype) {
            if ($profiletype[1] == $parameter[0] && $profiletype[2] == "keywords") {
                $handle = 1;
            } else {
                $icon = "default.png";
            }
        }
    
        if ($handle) {
            
            $searchline = "tagtype = " . $db->qstr($parameter[0]) . " AND tag = " . $db->qstr($parameter[1]) . "";
            $searchline = "(" . run("users:access_level_sql_where",$_SESSION['userid']) . ") and " . $searchline;
            $searchline = str_replace("owner","t.owner",$searchline);
            
            $parameter[1] = stripslashes($parameter[1]);

            if ($result = get_records_sql('SELECT DISTINCT u.* FROM '.$CFG->prefix.'tags t
                                          JOIN '.$CFG->prefix.'users u ON u.ident = t.owner
                                          WHERE '.$searchline)) {
            $profilesMsg = gettext("Profiles where");
$body = <<< END
            
    <h2>
        $profilesMsg
END;
                $body .= " '".gettext($parameter[0])."' = '".$parameter[1]."':";
                $body .= <<< END
    </h2>
END;
                $body .= <<< END
    <table class="userlist">
        <tr>
END;
                $i = 1;
                $w = 100;
                if (sizeof($result) > 4) {
                    $w = 50;
                }
                foreach($result as $key => $info) {
                    $friends_username = $info->username;
                    // $friends_name = htmlspecialchars(stripslashes($info->name), ENT_COMPAT, 'utf-8');
                    $friends_name = run("profile:display:name",$info->ident);
                    $info->icon = run("icons:get",$info->ident);
                    $friends_menu = run("users:infobox:menu",array($info->ident));
                    $body .= <<< END
        <td align="center">
            <p>
            <a href="{$CFG->wwwroot}{$friends_username}/">
            <img src="{$CFG->wwwroot}{$friends_username}/icons/{$info->icon}/w/{$w}" alt="{$friends_name}" border="0" /></a><br />
            <span class="userdetails">
                {$friends_name}
                {$friends_menu}
            </span>
            </p>
        </td>
END;
                    if ($i % 5 == 0) {
                        $body .= "</tr><tr>";
                    }
                    $i++;
                }
                $body .= <<< END
    </tr>
    </table>
END;
                $run_result .= $body;
            }
        }

?>