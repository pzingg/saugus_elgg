<?php

/*
 *    View files
 */

// Get owner and current folder
    
global $owner;
global $folder;
global $page_owner;

// Check to ensure we have access to this folder, if we're not in the root
$accessible = false;
if ($folder != -1) {
    if ($access = get_field('file_folders','access','files_owner',$owner,'ident',$folder)) {
        $accessible = run("users:access_level_check",$access);
    }
    //Need to see if this is my file store - if so, grant access no matter what (added for owned user support - JK)
	if ($page_owner == $_SESSION['userid'])
		$accessible = true;
}
        
// If we're in the root or an accessible folder, view it

if ($accessible || $folder == -1) {
    $run_result .= run("files:folder:view",$folder);
}
        
// If this is the user's own file repository, allow him or her to edit it

if (run("permissions:check", "files")) {
    
    $run_result .= run("files:folder:edit",$folder);
    
} else {
    
    //
    
}
    
?>