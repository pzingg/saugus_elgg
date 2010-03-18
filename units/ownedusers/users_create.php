<?php

	global $page_owner;
	global $USER;
	global $CFG;
	
	if (logged_on && $page_owner == $USER->ident && $USER->owner == -1) {
	
	$header = gettext("Create ".$CFG->owned_users_caption); // gettext variable
       $usersName = gettext("Display Name:"); // gettext variable
       $usersUsername = gettext("Username:"); // gettext variable
       $usersPassword = gettext("Password:"); // gettext variable
       $buttonValue = gettext("Create"); // gettext variable

       $run_result .= <<< END

<div class="owneduser_create">
	<p>
		&nbsp;
	</p>
	<h3>
		$header
	</h3>
	<form action="" method="post">
END;

		$run_result .= templates_draw(array(
														'context' => 'databox1',
														'name' => $usersName,
														'column1' => "<input type=\"text\" name=\"user_name\" value=\"$user_name\" />"
													)
													);
		$run_result .= templates_draw(array(
														'context' => 'databox1',
														'name' => $usersUsername,
														'column1' => "<input type=\"text\" name=\"user_username\" value=\"$user_username\" />"
													)
													);
		$run_result .= templates_draw(array(
														'context' => 'databox1',
														'name' => $usersPassword,
														'column1' => "<input type=\"password\" name=\"user_password\" value=\"\" />"
													)
													);
			
		$run_result .= <<< END
		<p>
			<input type="submit" value="$buttonValue" />
			<input type="hidden" name="action" value="owneduser:create" />
		</p>
		
	</form>
</div>

END;

	}

?>