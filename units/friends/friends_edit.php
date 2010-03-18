<?php
global $CFG;
// Given a user ID as a parameter, will display a list of friends

if (isset($parameter[0])) {
    
    $user_id = (int) $parameter[0];
    
    $result = get_records_sql('SELECT u.*,f.ident AS friendident FROM '.$CFG->prefix.'friends f
                              JOIN '.$CFG->prefix.'users u ON u.ident = f.friend
                              WHERE f.owner = ? AND u.user_type = ?',array($user_id,'person'));
    
    $body = <<< END
    <div class="networktable">
    <table>
        <tr>
END;
    $i = 1;
    if (!empty($result)) {
        foreach($result as $key => $info) {
            $w = 100;
            if (count($result) > 4) {
                $w = 50;
            }
            // $friends_name = htmlspecialchars(stripslashes($info->name), ENT_COMPAT, 'utf-8');
            $friends_name = run("profile:display:name", $info->ident);
            $info->icon = run("icons:get",$info->ident);
            $friends_menu = run("users:infobox:menu",array($info->ident));
            $body .= <<< END
        <td>
            <p>
            <a href="{$CFG->wwwroot}{$info->username}/">
            <img src="{$CFG->wwwroot}{$info->username}/icons/{$info->icon}/w/{$w}" alt="{$friends_name}" border="0" /></a><br />
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
    } else {
        if ($user_id == $_SESSION['userid']) {
            $body .= "<td><p>" . gettext("You don't have any friends listed! To add a user as a friend, click the 'friend' button underneath a user's icon.") . "</p></td>";
        } else {
            $body .= "<td><p>" . gettext("This user doesn't currently have any friends listed. Maybe if you list them as a friend, it'll start the ball rolling ..?") . "</p></td>";
        }
    }
    $body .= <<< END
    </tr>
    </table>
    </div>
END;


    $run_result = $body;
    
}

?>