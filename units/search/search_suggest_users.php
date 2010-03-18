<?php
global $CFG;
global $db;

if (isset($parameter)) {
    
    if ($CFG->dbtype == 'mysql') {
        $dbname = $db->qstr($parameter);
        $searchline = "SELECT DISTINCT name,username,MATCH(name) AGAINST (" . $dbname . ") AS score
                       FROM ".$CFG->prefix."users WHERE (MATCH(name) AGAINST (" . $dbname . ") > 0) LIMIT 20";
    } else {
        $dbname = $db->qstr("%" . $parameter . "%");
        $searchline = "SELECT DISTINCT name,username 
                       FROM ".$CFG->prefix."users WHERE (name LIKE " . $dbname . ") LIMIT 20";
    }
    
    if ($results = get_records_sql($searchline)) {
        $run_result .= "<h2>" . gettext("Matching users:") . "</h2><p>";
        foreach($results as $returned_name) {
            $run_result .= "<a href=\"" . url . $returned_name->username . '/">' . htmlspecialchars($returned_name->name) . "</a> <br />";
        }
        $run_result .= "</p>";
        
    }
    
}

?>