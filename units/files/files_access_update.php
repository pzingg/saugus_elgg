<?php
global $CFG;
// Update file access to match post access (ease of use feature to prevent access level inconsistencies)

if (isset($parameter[0])) {
	foreach ($parameter[0] as $fileid) {
	    $fileid = (int) $fileid;
	    if ($file = get_record('files','ident',$fileid)) {
	        if (run("users:access_level_check",$file->access) || $file->owner == $_SESSION['userid']) {	
	            $accesslevels = array("user","grou","comm","LOGG","PUBL");
	            $fileaccess = get_field("files","access","ident",$fileid);
	            if ((!empty($fileaccess)) && ($fileaccess != $parameter[1]->access)) {
	            	if (array_search(substr($fileaccess,0,4),$accesslevels) < array_search(substr($parameter[1]->access,0,4),$accesslevels))
	            		set_field("files","access",$parameter[1]->access,"ident",$fileid);
	            }
	        } 
	    } 
	}
}

?>