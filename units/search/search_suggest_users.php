<?php
global $CFG;
global $db;

if (isset($parameter)) {
    
    if ($CFG->dbtype == 'mysql') {
        $dbname = $db->qstr($parameter);
        $searchline = "SELECT DISTINCT name,username,MATCH(name) AGAINST (" . $dbname . ") AS score,ident,icon
                       FROM ".$CFG->prefix."users WHERE (MATCH(name) AGAINST (" . $dbname . ") > 0) LIMIT 20";
    } else {
        $dbname = $db->qstr("%" . $parameter . "%");
        $searchline = "SELECT DISTINCT name,username,ident,icon 
                       FROM ".$CFG->prefix."users WHERE (name LIKE " . $dbname . ") LIMIT 20";
    }
    
    if ($results = get_records_sql($searchline)) {
        $run_result .= "<h2>" . gettext("Matching users:") . "</h2><p>";
//        foreach($results as $returned_name) {
//            $run_result .= "<a href=\"" . url . $returned_name->username . '/">' . htmlspecialchars($returned_name->name) . "</a> <br />";
//        }
//        $run_result .= "</p>";
			$body = "<table><tr>";
	        $i = 1;
	        $w = 100;
	        if (sizeof($users) > 4) {
	            $w = 50;
	        }
	        foreach($results as $info) {
	            $friends_userid = $info->ident;
	            $friends_name = htmlspecialchars(stripslashes($info->name), ENT_COMPAT, 'utf-8');
	            $info->icon = run("icons:get",$info->ident);
	            $body .= <<< END
        <td align="center">
            <p>
            <a href="{$CFG->wwwroot}{$info->username}">
            <img src="{$CFG->wwwroot}{$info->username}/icons/{$info->icon}/w/{$w}" alt="{$friends_name}" border="0" /></a><br />
            <span class="userdetails">
                {$friends_name}
            </span>
            </p>
        </td>
END;
	                if ($i % 5 == 0) {
	                    $body .= "\n</tr><tr>\n";
	                }
	                $i++;
	        }
	        $body .= "</tr></table>";
	        $run_result .= $body;
	    }
	    
    
}

?>