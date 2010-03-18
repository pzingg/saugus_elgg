<?php

//    ELGG weblog view page

// Run includes
require_once(dirname(dirname(__FILE__))."/includes.php");

run("profile:init");
run("friends:init");
run("weblogs:init");

define("context", "weblog");

global $profile_id;
global $individual;

$individual = 1;

$post = optional_param('post',0,PARAM_INT);
if (!empty($post)) {
    
    $where = run("users:access_level_sql_where",$_SESSION['userid']);
    
    if (!$post = get_record_select('weblog_posts','('.$where.') AND ident = '.$post)) {
        $post = new StdClass;
        $post->weblog = -1;
        $post->owner = -1;
        $post->title = gettext("Access denied or post not found");
        $post->posted = time();
        $post->ident = -1;
        $post->body = gettext("Either this blog post doesn't exist or you don't currently have access privileges to view it.");
    }
    
    global $page_owner;
    global $profile_id;
    $profile_id = $post->weblog;
    $page_owner = $post->weblog;
    
    $title = run("profile:display:name") . " :: " . gettext("Weblog") . " :: " . stripslashes($post->title);
    templates_page_setup();
    
    $time = gmstrftime("%B %d, %Y",$post->posted);
    $body = "<h2 class=\"weblog_dateheader\">$time</h2>\n";
    
    $body .= run("weblogs:posts:view:individual",$post);
    
    $body = templates_draw(array(
                                 'context' => 'contentholder',
                                 'title' => $title,
                                 'body' => $body
                                 )
                           );
    
    echo templates_page_draw( array(
                                          $title, $body
                                          )
             );
    
}

?>