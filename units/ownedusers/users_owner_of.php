<?php

	// Given a user ID as a parameter, will display a list of owned users

	global $CFG;
	global $USER;
	
	if (isset($parameter[0])) {

		$user_id = (int) $parameter[0];
		
		$result = get_records_select('users',"owner = ? AND user_type = ?",array($user_id,'person'));
									
		$body = <<< END
	<table class="networktable">
		<tr>
END;
    $i = 1;
    if (!empty($result)) {
        foreach($result as $key => $info) {
            $w = 100;
            if (sizeof($result) > 4) {
                $w = 50;
            }
            // $friends_name = htmlspecialchars(stripslashes($info->name), ENT_COMPAT, 'utf-8');
            $friends_name = run("profile:display:name", $info->ident);
            $info->icon = run("icons:get",$info->ident);
            $friends_menu = "";
			if (logged_on && (($info->owner == $USER->ident) || run("users:flags:get", array("admin", $_SESSION['userid'])))) {
				$friends_menu = "<br />[<a href=\"".$CFG->wwwroot."_userdetails/?context=profile&profile_id=".$info->ident."\">".gettext("Edit")."</a>]";
				$friends_menu .= "[<a href=\"".$CFG->wwwroot."_ownedusers/moveownedusers.php?owner=".$user_id."&profile_id=".$info->ident."\">".gettext("Move")."</a>]";
			}

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
				$body .= "<td><p>". gettext("You don't own any ".$CFG->owned_users_caption.". Why not create one?") ."</p></td>";
			} else {
				$body .= "<td><p>". gettext("This user is not currently moderating any ".$CFG->owned_users_caption.".") ."</p></td>";
			}
		}
		$body .= <<< END
	</tr>
	</table>
END;


		$run_result = $body;

	}

?>