<?php

    /*
    *    File repository plug-in
    */

    // Functions to perform upon initialisation
        $function['files:init'][] = path . "units/files/files_init.php";
        $function['files:init'][] = path . "units/files/metadata_defaults.php";
        $function['init'][] = path . "units/files/inline_mimetypes.php";
    
    // Actions to perform
        $function['files:init'][] = path . "units/files/files_actions.php";

    // Init for search
        $function['search:init'][] = path . "units/files/files_init.php";
        $function['search:all:tagtypes'][] = path . "units/files/function_search_all_tagtypes.php";
        
    // Function to search through weblog posts
        $function['search:display_results'][] = path . "units/files/function_search.php";
        $function['search:display_results:rss'][] = path . "units/files/function_search_rss.php";
        
    // Determines whether or not a file should be displayed in the browser
        $function['files:mimetype:inline'][] = path . "units/files/files_mimetype_inline.php";
        
    // View files
        $function['files:view'][] = path . "units/files/files_view.php";

    // View the contents of a specific folder
        $function['files:folder:view'][] = path . "units/files/folder_view.php";
        
    // Edit the contents of a specific folder
        $function['files:folder:edit'][] = path . "units/files/folder_edit.php";
        
    // Edit the metadata for a specific file
        $function['files:edit'][] = path . "units/files/edit_file.php";
        $function['folder:select'][] = path . "units/files/select_folder.php";
    
    // Edit metadata
        $function['metadata:edit'][] = path . "units/files/metadata_edit.php";
        
    // Turn file ID into a link
        $function['files:links:make'][] = path . "units/files/files_links_make.php";
        $function['files:links:make:rss'][] = path . "units/files/files_links_make_rss.php";
        
    // Update file access rights to match post rights automatically
    	$function['files:access:update'][] = path . "units/files/files_access_update.php";
        
    // Load default template
        $function['init'][] = path . "units/files/default_templates.php";

    // Allow users to embed files in weblog posts
//        $function['weblogs:posts:add:fields'][] = path . "units/files/weblogs_posts_add_fields.php";
        $function['weblogs:posts:edit:fields:files'][] = path . "units/files/weblogs_posts_add_fields.php";
        $function['weblogs:text:process'][] = path . "units/files/weblogs_text_process.php";
                    
    // Log on bar down the right hand side
        $function['display:sidebar'][] = path . "units/files/files_user_info_menu.php";
        
    // Template preview
        $function['templates:preview'][] = path . "units/files/templates_preview.php";

    // Establish permissions
        $function['permissions:check'][] = path . "units/files/permissions_check.php";

    // Actions to perform when an access group is deleted
        $function['groups:delete'][] = path . "units/files/groups_delete.php";

    // Publish static RSS file of files
        $function['files:rss:getitems'][] = path . "units/files/function_rss_getitems.php";
        $function['files:rss:publish'][] = path . "units/files/function_rss_publish.php";
        
?>