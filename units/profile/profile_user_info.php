<?php
global $CFG,$USER;
global $page_owner;
$textlib = textlib_get_instance();
    
// If this is someone else's portfolio, display the user's icon
$run_result .= "<li id=\"sidebar_user\">";

$info = get_record('users','ident',$page_owner);

if (!$tagline = get_field_sql('SELECT value FROM '.$CFG->prefix.'profile_data
                               WHERE owner = '.$page_owner." AND name = 'minibio' 
                               AND (".run("users:access_level_sql_where",$USER->ident).")")) {
    $tagline = "&nbsp;";
}

$ul_username = run("users:id_to_name", $page_owner);

$info->icon = run("icons:get", $page_owner);

$icon = '<img alt="" src="'.url. $ul_username.'/icons/'.$info->icon.'/h/67/w/67" border="0" />'; // height is the important one here.
// $name = stripslashes($info->name); 
$name = run("profile:display:name");
$url = url . $info->username . "/";

$lmshosts = '';
if ($info->ident == $USER->ident) {
    // fetch aliases
    if ($aliases = get_records('users_alias','user_id',$USER->ident)) {
        foreach ($aliases as $alias) {
            if (!empty($CFG->lmshosts) && is_array($CFG->lmshosts) && array_key_exists($alias->installid,$CFG->lmshosts)) {
                $name = $alias->installid;
                if (!empty($CFG->lmshosts[$alias->installid]['name'])) {
                    $name = $CFG->lmshosts[$alias->installid]['name'];
                }
                $lmshosts .= '<a href="'.$CFG->lmshosts[$alias->installid]['baseurl'].'">'.$name.'</a><br />';
            }
        }
    }
}
if (!empty($lmshosts)) {
    $lmshosts = gettext('Your LMS hosts').':<br />'.$lmshosts;
}

$body = templates_draw(array(
                             'context' => 'ownerbox',
                             'name' => $name,
                             'profileurl' =>  $url,
                             'usericon' => $icon,
                             'tagline' => $tagline,
                             'lmshosts' => $lmshosts,
                             'usermenu' => run("users:infobox:menu:text",array($page_owner))
                             )
                       );
                       
if ($page_owner != -1) {
    if ($page_owner != $_SESSION['userid']) {
        $title = gettext("Profile Owner");
    } else {
        $title = gettext("You");
    }
}

$run_result .= templates_draw(array(
                                    'context' => 'sidebarholder',
                                    'title' => $title,
                                    'body' => $body
                                    )
                              );
$run_result .= "</li>";

?>