<?php
global $CFG;
global $RSS_ENCLOSURE;
// Turn file ID into a proper link

if (isset($parameter)) {
    
    $fileid = (int) $parameter;
    if ($file = get_record('files','ident',$fileid)) {
        if (run("users:access_level_check",$file->access) || $file->owner == $_SESSION['userid']) {
            if (!in_array(run("files:mimetype:inline",$CFG->dataroot.$file->location), $data['mimetype:inline'])) {
                require_once($CFG->dirroot.'lib/filelib.php');
                $mimeinfo = mimeinfo('type',$file->location);
                if ($mimeinfo == "audio/mpeg" || $mimeinfo == "audio/mp3" || $mimeinfo == "audio/mp4" || $mimeinfo == "video/quicktime" || $mimeinfo == "video/mp4") {
                    $filepath = $CFG->wwwroot . run("users:id_to_name",$file->owner) . "/files/" . $file->folder . "/" . $file->ident . "/" . $file->originalname;
                    $filesize = filesize($CFG->dataroot . $file->location);
                    $RSS_ENCLOSURE .= "<enclosure url=\"$filepath\" length=\"$filesize\" type=\"$mimeinfo\" />";
                } 
                $run_result .= "<a href=\"";
                $run_result .= url . run("users:id_to_name",$file->owner) . "/files/" . $file->folder . "/" . $file->ident . "/" . $file->originalname;
                $run_result .= "\" >";
                $run_result .= stripslashes($file->title);
                $run_result .= "</a> " . $mimeinfo;
            } else {
                list($width, $height, $type, $attr) = getimagesize($CFG->dataroot.$file->location);
                if ($width > 400 || $height > 400) {
//                    $run_result .= "<a href=\"";
//                    $run_result .= url . run("users:id_to_name",$file->owner) . "/files/" . $file->folder . "/" . $file->ident . "/" . $file->originalname;
//                    $run_result .= "\" >";
//                    $run_result .= stripslashes($file->title);
//                    $run_result .= "</a>";
					$run_result .= "<a href=\"".url . run("users:id_to_name",$file->owner) . "/files/" . $file->folder . "/" . $file->ident . "/" . $file->originalname."\" target=\"_blank\"><img src=\"";
                    $run_result .= url . run("users:id_to_name",$file->owner) . "/files/" . $file->folder . "/" . $file->ident . "/" . $file->originalname;
                    $run_result .= "\" width=\"400\" alt=\"".htmlspecialchars(stripslashes($file->title), ENT_COMPAT, 'utf-8')."\" border=\"0\" /></a>";
                } else {
                    $run_result .= "<img src=\"";
                    $run_result .= url . run("users:id_to_name",$file->owner) . "/files/" . $file->folder . "/" . $file->ident . "/" . $file->originalname;
                    $run_result .= "\" $attr alt=\"".htmlspecialchars(stripslashes($file->title), ENT_COMPAT, 'utf-8')."\" />";
                }
            }
        } else {
            $run_result .= "<b>[" . gettext("You do not have permission to access this file") . "]</b>";
        }
    } else {
        $run_result .= "<b>[" . gettext("File does not exist") . "]</b>";
    }
}

?>