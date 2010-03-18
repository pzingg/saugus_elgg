<?php
global $CFG;
global $page_owner;

if ($page_owner != -1) {
    if (run("users:type:get", $page_owner) == "person") {
        if ($result = get_records_sql('SELECT DISTINCT u.ident,u.username,u.name FROM '.$CFG->prefix.'friends f
                                       JOIN '.$CFG->prefix.'users u ON u.ident = f.friend
                                       WHERE f.owner = ? AND u.user_type = ? AND u.owner != ?',
                                      array($page_owner,'community',$page_owner))) {
            $body = "<ul>";
            foreach($result as $row) {
                $row->name = run("profile:display:name",$row->ident);
                $body .= "<li><a href=\"" . url . $row->username . "/\">" . $row->name . "</a></li>";
            }
            $body .= "</ul>";
            $run_result .= "<li id=\"community_membership\">";
            $run_result .= templates_draw(array(
                                                'context' => 'sidebarholder',
                                                'title' => gettext("Community memberships"),
                                                'body' => $body
                                                )
                                          );
            $run_result .= "</li>";
        } else {
            $run_result .= "";
        }
    } else if (run("users:type:get", $page_owner) == "community") {
        $friends = array();
        if ($result = get_records_sql('SELECT DISTINCT u.ident,1 FROM '.$CFG->prefix.'friends f
                                JOIN '.$CFG->prefix.'users u ON u.ident = f.owner
                                WHERE f.friend = ? LIMIT 8',array($page_owner))) {
            foreach($result as $row) {
                $friends[] = (int)$row->ident;
            }
        }
        $run_result .= "<li id=\"community_membership\">";
        $run_result .= run("users:infobox",
                           array(
                                 gettext("Members"),
                                 $friends,
                                 "<a href=\"".url."_communities/members.php?owner=$profile_id\">" . gettext("Members") . "</a>"
                                 )
                           );
        $run_result .= "</li>";
    }
}


?>