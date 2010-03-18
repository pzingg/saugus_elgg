<?php

$sitename = sitename;    
// Function to recursively view folders
    
function viewfolder($folderid, $userid, $level) {

    $prefix = "";
    for ($i = 0; $i < $level; $i++) {
        $prefix .= "&gt;";
    }
    $fileprefix = $prefix . "&gt;";
    
    if ($folderid == -1) {
        $body = <<< END
                <option value="">ROOT</option>
END;
    } else {
        $current_folder = get_record('file_folders','owner',$userid,'ident',$folderid);
        $name = strtoupper(stripslashes($current_folder->name));
        $body = <<< END
                    <option value="">{$prefix} {$name}</option>
END;
    }
    if ($files = get_records_select('files',"owner = ? AND folder = ?",array($userid,$folderid))) {
        foreach($files as $file) {
            $filetitle = stripslashes($file->title);
            $body .= <<< END
                    
                    <option value="{$file->ident}">{$fileprefix} {$filetitle}</option>
END;
        }
    }
    
    if ($folders = get_records_select('file_folders',"owner = ? AND parent = ? ",array($userid,$folderid))) {
        foreach($folders as $folder) {
            $body .= viewfolder($folder->ident, $userid, $level + 1);
        }
    }
    return $body;
}

// Add "insert file" field to weblog post uploads

if (isset($parameter)) {
    
    if (is_array($parameter)) {
        $userid = (int) $parameter[0];
    } else {
        $userid = (int) $parameter;
    }
    
    $extrafunctions = run("files:embed:js");
    
    $embedFile = sprintf(gettext("Embed a file from your %s file storage:"), $sitename);    
    $run_result .= <<< END
<script language="javascript" type="text/javascript">
<!--

    function addFile(form) {
        if (form.weblog_add_file.selectedIndex != "") {
            form.new_weblog_post.value = form.new_weblog_post.value + "{{file:" + form.weblog_add_file.options[form.weblog_add_file.selectedIndex].value + "}}";
            $extrafunctions
        }
    }

// -->
</script>
            
            
            <p>
      $embedFile<br />
                <select name="weblog_add_file" id="weblog_add_file">
        
END;

    $run_result .= viewfolder(-1, $userid, 0);

    $addCode = gettext("This will add a code to your weblog post that will be converted into an embedded file."); // gettext variable
    $buttonValue = gettext("Add"); // gettext value
    $run_result .= <<< END
        
                </select>
                <input type="button" value="$buttonValue" onclick="addFile(this.form)" /><br />
                ($addCode)
            </p>
        
END;

}

?>