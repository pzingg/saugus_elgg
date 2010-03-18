<?php

// User icon serving script.
// Usage: http://URL/{username}/icons/{icon_id}

// Run includes
require_once(dirname(dirname(__FILE__))."/includes.php");
$textlib = textlib_get_instance();

// If an ID number for the file has been specified ...
$id = optional_param('id',0,PARAM_INT);
$default = false;
if (empty($id)) {
    $default = true;
}
// ... and the file exists ...
if (!$file = get_record('icons','ident',$id)) {
    $default = true;
}
// get the user who belongs to this icon..
if (empty($file) || !$user = get_record('users','ident',$file->owner)) {
    $default = true;
}
if (empty($default)) {
    $upload_folder = $textlib->substr($user->username,0,1);
    $filepath = $CFG->dataroot . "icons/" . $upload_folder . "/" . $user->username . "/".$file->filename; 
    if (!file_exists($filepath)) {
        $default = true;
    }
}

require_once($CFG->dirroot . 'lib/filelib.php');
require_once($CFG->dirroot . 'lib/iconslib.php');
    
if (!empty($default)) {
    $filepath = $CFG->dirroot.'_icons/data/default.png';
    $mimetype = 'image/png';
} else {
    $mimetype = mimeinfo('type', $file->filename);
}


// Then output some appropriate headers and send the file data!

// see if we must resize it.
$constraint1 = strtolower(optional_param('constraint1'));
$size1 = optional_param('size1', PARAM_INT);
$constraint2 = strtolower(optional_param('constraint2'));
$size2 = optional_param('size2', PARAM_INT);

// if size == 100, leave it.
$phpthumbconfig = array();
if (!empty($constraint1) && !empty($size1) && ($constraint1 == 'h' || $constraint1 == 'w') && $size1 != 100) {
    $phpthumb = true;
    $phpthumbconfig[$constraint1] = $size1;
}
if (!empty($constraint2) && !empty($size2) && ($constraint2 == 'h' || $constraint2 == 'w') && $size2 != 100) {
    $phpthumb = true;
    $phpthumbconfig[$constraint2] = $size2;
}

// user icons are public
header("Cache-Control: public");

if (!empty($phpthumb)) {
    // let phpthumb manipulate the image
    spit_phpthumb_image($filepath, $phpthumbconfig);
} else {
    // output the image directly
    spitfile_with_mtime_check ($filepath, $mimetype);
}

?>