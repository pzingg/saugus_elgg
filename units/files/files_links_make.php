<?php
global $CFG;
// Turn file ID into a proper link

if (isset($parameter)) {
    
    $fileid = (int) $parameter;
    if ($file = get_record('files','ident',$fileid)) {
        if (run("users:access_level_check",$file->access) || $file->owner == $_SESSION['userid']) {
            if (!in_array(run("files:mimetype:inline",$CFG->dataroot.$file->location), $data['mimetype:inline'])) {
                require_once($CFG->dirroot.'lib/filelib.php');
                $mimeinfo = mimeinfo('type',$file->location);
                if ($mimeinfo == "audio/mpeg" || $mimeinfo == "audio/mp3") {
                    $filepath = $CFG->wwwroot . run("users:id_to_name",$file->owner) . "/files/" . $file->folder . "/" . $file->ident . "/" . $file->originalname;
                    $filetitle = urlencode(stripslashes($file->title));
                    $run_result .= "<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\"
codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0\"
width=\"400\" height=\"15\" >
    <param name=\"allowScriptAccess\" value=\"sameDomain\"/>
    <param name=\"movie\" value=\"" . $CFG->wwwroot . "_files/mp3player/xspf_player_slim.swf?song_url=$filepath&song_title=$filetitle\"/>
    <param name=\"quality\" value=\"high\"/>
    <param name=\"bgcolor\" value=\"#E6E6E6\"/>
    <embed src=\"" . $CFG->wwwroot . "_files/mp3player/xspf_player_slim.swf?song_url=$filepath&amp;song_title=$filetitle\"
    quality=\"high\" bgcolor=\"#E6E6E6\" name=\"xspf_player\" allowscriptaccess=\"sameDomain\"
    type=\"application/x-shockwave-flash\"
    pluginspage=\"http://www.macromedia.com/go/getflashplayer\"
    align=\"center\" height=\"15\" width=\"400\"> </embed>
</object><br /><small>[ <a href=\"$filepath\">Download - audio/mp3</a> ]</small>";
                } else if ($mimeinfo == "application/x-sb") {
                    $filepath = run("users:id_to_name",$file->owner) . "/files/" . $file->folder . "/" . $file->ident . "/" . $file->originalname;
                    $filetitle = urlencode(stripslashes($file->title));
                    $run_result .= "<applet id=\"ProjectApplet\" style=\"display:block\" code=\"ScratchApplet\" codebase=\"".$CFG->wwwroot."_files\" archive=\"ScratchApplet.jar\" height=\"387\" width=\"482\">
	<param name=\"project\" value=\"../$filepath\">
</applet>";
                } else if ($mimeinfo == "application/x-shockwave-flash") {
                    $filepath = $CFG->wwwroot . run("users:id_to_name",$file->owner) . "/files/" . $file->folder . "/" . $file->ident . "/" . $file->originalname;
                    $filetitle = urlencode(stripslashes($file->title));
                    $run_result .= "<object width=\"400\" height=\"300\">
	<param name=\"movie\" value=\"$filepath\"></param>
    <param name=\"allowFullScreen\" value=\"true\"></param>
    <param name=\"allowscriptaccess\" value=\"always\"></param>
    <embed src=\"$filepath\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" width=\"400\" height=\"300\">
    </embed>
</object>";
                } else if ($mimeinfo == "audio/mp4") {
                    $filepath = $CFG->wwwroot . run("users:id_to_name",$file->owner) . "/files/" . $file->folder . "/" . $file->ident . "/" . $file->originalname;
                    $filetitle = urlencode(stripslashes($file->title));
                    $run_result .= "<embed width=\"320\" height=\"30\" controller=\"false\" autoplay=\"true\" target=\"myself\" src=\"".$CFG->wwwroot."_files/preload-audio.mov\" href=\"$filepath\" autohref=\"false\" type=\"audio/mp4\" pluginspage=\"http://www.apple.com/quicktime/download/\"></embed>";
                } else if (($mimeinfo == "video/quicktime") || ($mimeinfo == "video/mp4")) {
                    $filepath = $CFG->wwwroot . run("users:id_to_name",$file->owner) . "/files/" . $file->folder . "/" . $file->ident . "/" . $file->originalname;
                    $filetitle = urlencode(stripslashes($file->title));
                    $run_result .= "<embed width=\"400\" height=\"320\" controller=\"true\" autoplay=\"true\" target=\"myself\" src=\"".$CFG->wwwroot."_files/preload.mov\" href=\"<$filepath> T<myself>\" autohref=\"false\" type=\"$mimeinfo\" pluginspage=\"http://www.apple.com/quicktime/download/\"></embed>";
                } else {
                    $run_result .= "<a href=\"";
                    $run_result .= url . run("users:id_to_name",$file->owner) . "/files/" . $file->folder . "/" . $file->ident . "/" . $file->originalname;
                    $run_result .= "\" >";
                    $run_result .= stripslashes($file->title);
                    $run_result .= "</a> " . $mimeinfo;
                }
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
            $run_result .= "<b>[ " . gettext("You do not have permission to access this file");
            if (! logged_on) $run_result .= " - <a href=\"".url."login/index.php\">" . gettext("Login") . "</a>";
            $run_result .= " ]</b>";
        }
    } else {
        $run_result .= "<b>[" . gettext("File does not exist") . "]</b>";
    }
}


?>
