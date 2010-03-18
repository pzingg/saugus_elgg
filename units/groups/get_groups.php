<?php

global $CFG;

// Gets all the groups owned by a particular user, as specified in $parameter[0],
// and return it in a data structure with the idents of all the users in each group

$ident = (int) $parameter[0];

//if (!isset($_SESSION['groups_cache']) || (time() - $_SESSION['groups_cache']->created > 60)) {

$tempdata = "";
$groupslist = array();

if ($groups = get_records('groups','owner',$ident)) {
    foreach($groups as $group) {
        
        $tempdata = "";
        
        // @unset($data);
        $tempdata->name = stripslashes($group->name);
        $tempdata->ident = $group->ident;
        $tempdata->access = $group->access;
        $members = get_records_sql("select gm.user_id,
                                    u.name from ".$CFG->prefix."group_membership gm
                                    join ".$CFG->prefix."users u on u.ident = gm.user_id
                                    where gm.group_id = ?", array($tempdata->ident));
        $tempdata->members = $members;
        
        $groupslist[] = $tempdata;
        
    }
}

$_SESSION['groups_cache']->created = time();
$_SESSION['groups_cache']->data = $groupslist;

//}

$run_result = $_SESSION['groups_cache']->data;

?>