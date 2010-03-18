<?php

global $page_owner;

if ($page_owner != -1) {
    if (run("users:type:get", $page_owner) == "person") {
        if ($result = get_records_select('users',"owner = ? AND user_type = ?",array($page_owner,'community'))) {
            $body = "<ul>";
            foreach($result as $row) {
                    $row->name = run("profile:display:name",$row->ident);
                    $body .= "<li><a href=\"" . url . $row->username . "/\">" . $row->name . "</a></li>";
            }
            $body .= "</ul>";
            // $run_result .= $body;
            $run_result .= "<li id=\"community_owned\">";
            $run_result .= templates_draw(array(
                                                'context' => 'sidebarholder',
                                                'title' => gettext("Owned communities"),
                                                'body' => $body
                                                )
                                          );
            $run_result .= "</li>";
        } else {
            $run_result .= "";
        }
    }
}

?>