<?php

	global $USER;
	global $page_owner;

    if ($parameter[1] == "PRIVATE") {
        $parameter[1] = "user" . $_SESSION['userid'];
    }

    $run_result .= "<select name=\"". $parameter[0] . "\">";

	if ($USER->owner == -1) { 	//added to restrict access controls to private for owned users (students)
	    if (!empty($data['access'])) {
	        foreach($data['access'] as $access) {
	            if ($parameter[1] == $access[1] && $parameter[1] != "") {
	                $selected = ' selected="selected" ';
	            } else {
	                $selected = "";
	            }
	            $run_result .= <<< END
	    <option value="{$access[1]}" {$selected}>
	        {$access[0]}
	    </option>
END;
	        }
	    }
	}
	else {
		if (isset($parameter[2])) {
			$poster = get_record('users','ident',$parameter[2]);
		} else {
			$poster = get_record('users','ident',$page_owner);
		}
		if ((!logged_on) && (run("users:flags:get",array("publiccomments",$poster->ident)))) {
			if ($poster->owner == -1) { //not an owned user - public comments allowed
				$run_result .= "<option value=\"PUBLIC\">Public</option>";
				$run_result .= "<option value=\"user" . $poster->ident . "\">Private</option>";
			} else {
				$run_result .= "<option value=\"user" . $poster->owner . "\">Private</option>";
			}
		} else {
			$run_result .= "<option value=\"user" . $USER->owner . "\">Private</option>";
		}
	}

    $run_result .= "</select>";
    
?>