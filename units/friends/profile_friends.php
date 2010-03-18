<?php
global $CFG;
global $page_owner;

if ($page_owner != -1 && run("users:type:get", $page_owner) == "person") {
    $friends = array();
    if ($result = get_records_sql('SELECT DISTINCT u.ident,1 FROM '.$CFG->prefix.'friends f
                                   JOIN '.$CFG->prefix.'users u ON u.ident = f.friend
                                   WHERE f.owner = ? AND u.user_type = ? LIMIT 8',array($page_owner,'person'))) {
        foreach($result as $row) {
            $friends[] = (int) $row->ident;
        }
    }
        $run_result .= "<li id=\"sidebar_friends\">";
    if ($page_owner != $_SESSION['userid']) {
        $run_result .= run("users:infobox",
                           array(
                                 gettext("Friends"),
                                 $friends,
                                 "<a href=\"".url."_friends/?owner=$profile_id\">[" . gettext("View all Friends") . "]</a>"
                                 )
                           );
        
    } else {
        $run_result .= run("users:infobox",
                           array(
                                 gettext("Your Friends"),
                                 $friends,
                                 "<a href=\"".url.$_SESSION['username']."/friends/\">[" . gettext("View all Friends") . "]</a>"
                                 )
                           );
    }
        $run_result .= "</li>";
    
}

?>