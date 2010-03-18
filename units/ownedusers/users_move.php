<?php

	// Given a user ID as a parameter, will display a list of owned users

	global $CFG;
	
	if (isset($parameter[0])) {

		$user_id = (int) $parameter[0];
		$profile_id = (int) $parameter[1];
		$username = run("profile:display:name", $profile_id);
		$caption = gettext("Please select a new owner for "); // gettext variable
		
		$result = get_records_select('users',"owner = ? AND user_type = ? and ident <> ? and ident <> ?",array(-1,'person',$user_id,1),'name');
									
		$body = <<< END
	<p>$caption $username</p>
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

            $body .= <<< END
        <td>
            <p>
            <a href="{$CFG->wwwroot}_ownedusers/owned.php?owner={$user_id}&action=owneduser:move&profile_id={$profile_id}&newowner={$info->ident}">
            <!-- <img src="{$CFG->wwwroot}{$info->username}/icons/{$info->icon}/w/{$w}" alt="{$friends_name}" border="0" /></a><br /> -->
            <span class="userdetails">
                {$friends_name}
                {$friends_menu}</a>
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