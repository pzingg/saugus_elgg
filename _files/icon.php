<?php

// Icon script

// Run includes
require_once(dirname(dirname(__FILE__))."/includes.php");

// Initialise functions for user details, icon management and profile management
run("userdetails:init");
run("profile:init");
run("files:init");

// If an ID number for the file has been specified ...
$id = optional_param('id',0,PARAM_INT);
if (!empty($id)) {
    // ... and the file exists ...
    if ($file = get_record('files','ident',$id)) {
        if (run("users:access_level_check",$file->access) == true) {
            
            require_once($CFG->dirroot . 'lib/filelib.php');
            require_once($CFG->dirroot . 'lib/iconslib.php');
            
            // "Cache-Control: private" to allow a user's browser to cache the file, but not a shared proxy
            // Also to override PHP's default "DON'T EVER CACHE THIS EVER" header
            header("Cache-Control: private");
            
            // Then output some appropriate headers and send the file data!
            $mimetype = mimeinfo('type',$file->originalname);
            if ($mimetype == "image/jpeg" || $mimetype == "image/png") {
                // file is an image
                $phpthumbconfig['w'] = 90;
                spit_phpthumb_image($CFG->dataroot . $file->location, $phpthumbconfig);
                
            } else {
                // file is a file
                spitfile_with_mtime_check ($CFG->dirroot . "_files/file.png", "image/png");
            }
            
        }
    }
}

?>