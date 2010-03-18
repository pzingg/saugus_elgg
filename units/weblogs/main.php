<?php

    /*
    *    Weblog plug-in
    */

    // Functions to perform upon initialisation
        $function['weblogs:init'][] = path . "units/weblogs/weblogs_init.php";
        $function['weblogs:init'][] = path . "units/weblogs/weblogs_actions.php";
        
    // Load default template
        $function['init'][] = path . "units/weblogs/default_template.php";
    
    // Init for search
        $function['search:init'][] = path . "units/weblogs/weblogs_init.php";
        $function['search:all:tagtypes'][] = path . "units/weblogs/function_search_all_tagtypes.php";
        
    // Function to search through weblog posts
        $function['search:display_results'][] = path . "units/weblogs/function_search.php";
        $function['search:display_results:rss'][] = path . "units/weblogs/function_search_rss.php";
        
    // Edit / create weblog posts
        $function['weblogs:edit'][] = path . "units/weblogs/weblogs_edit.php";
        $function['weblogs:posts:add'][] = path . "units/weblogs/weblogs_posts_add.php";
        $function['weblogs:posts:edit'][] = path . "units/weblogs/weblogs_posts_edit.php";
        
    // View weblog posts
        $function['weblogs:view'][] = path . "units/weblogs/weblogs_post_field_wrapper.php";
        $function['weblogs:view'][] = path . "units/weblogs/weblogs_view.php";
        $function['weblogs:posts:view'][] = path . "units/weblogs/weblogs_posts_view.php";
        $function['weblogs:posts:view:individual'][] = path . "units/weblogs/weblogs_posts_view.php";
        $function['weblogs:friends:view'][] = path . "units/weblogs/weblogs_friends_view.php";
        $function['weblogs:everyone:view'][] = path . "units/weblogs/weblogs_all_users_view.php";
        $function['weblogs:text:process'][] = path . "units/weblogs/weblogs_text_process.php";
        $function['weblogs:archives:view'][] = path . "units/weblogs/archives_view.php";
        $function['weblogs:archives:month:view'][] = path . "units/weblogs/weblogs_view_month.php";
        
    // Mark posts as interesting (or not)
        $function['weblogs:interesting:form'][] = path . "units/weblogs/display_interesting_post_form.php";
        
    // Edit / create weblog comments
        $function['weblogs:comments:add'][] = path . "units/weblogs/weblogs_comments_add.php";
        
    // Log on bar down the right hand side
        // $function['profile:log_on_pane'][] = path . "units/weblogs/weblogs_user_info_menu.php";
        $function['display:sidebar'][] = path . "units/weblogs/weblogs_user_info_menu.php";
        
    // Weblog preview
        $function['templates:preview'][] = path . "units/weblogs/templates_preview.php";
        
    // Establish permissions
        $function['permissions:check'][] = path . "units/weblogs/permissions_check.php";
        
    // Actions to perform when an access group is deleted
        $function['groups:delete'][] = path . "units/weblogs/groups_delete.php";
        
    // Publish static RSS file of posts
        $function['weblogs:rss:getitems'][] = path . "units/weblogs/function_rss_getitems.php";
        $function['weblogs:rss:publish'][] = path . "units/weblogs/function_rss_publish.php";
        
    // Removing function from weblogs_init.php
        $function['weblogs:html_activate_urls'][] = path . "units/weblogs/function_html_activate_urls.php";
?>