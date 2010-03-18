<?php
global $CFG;
require_once($CFG->dirroot.'lib/uploadlib.php');
/*
 *    Function to publish weblog posts as RSS, either a static file or return output
 *    
 *    $parameter[0] is the numeric id of the user to publish
 *    
 *    $parameter[1] is true to return a string of RSS, or false to publish static file.
 *        (Defaults to publishing file)
 *
 */

    $run_result = false;
    
    if (isset($parameter) && is_array($parameter)) {
        
        $userid = (int) $parameter[0];
        if ($userid > 0) {
            $username = run("users:id_to_name", $userid);
        }
        if ($username) {
            
            // make output dirs if they don't already exist
            $publish_folder = substr($username,0,1);
            
            $publish_folder = "rss/" . $publish_folder . "/" . $username ;
            
            make_upload_directory($publish_folder,false);
            
            $rssfile = $CFG->dataroot.$publish_folder.'/weblog.xml';
            
            //generate rss
            $sitename = sitename;
            $rssweblog = gettext("Weblog");
            
            $info = get_record('users','ident',$userid);
            $name = stripslashes($info->name);
            $username = $info->username;
            $mainurl = $CFG->wwwroot . $username . "/weblog/";
            $rssurl = $mainurl . "rss/";
            $rssdescription = sprintf(gettext("The weblog for %s, hosted on %s."),$name,$sitename);
            $output = <<< END
<?xml-stylesheet type="text/xsl" href="{$rssurl}rssstyles.xsl"?>
<rss version='2.0'   xmlns:dc='http://purl.org/dc/elements/1.1/'>
    <channel xml:base='$mainurl'>
        <title><![CDATA[$name : $rssweblog]]></title>
        <description><![CDATA[$rssdescription]]></description>
        <generator>Elgg</generator>
        <link>$mainurl</link>
END;
            
            // WEBLOGS
            $output .= run("weblogs:rss:getitems", array($userid, 10, ""));
            $output .= <<< END

    </channel>
</rss>
END;
            
            if ($parameter[1] === true) {
                
                $run_result = $output;
                
            } else {
                
                // write to file
                if ($handle = fopen($rssfile, "wb")) {
                    $writeresult = fwrite($handle, $output);
                    $closeresult = fclose($handle);
                    if ($writeresult && $closeresult) {
                        $run_result = true;
                    }
                } else {
                    error_log("failed to open $rssfile for writing!");
                }
                
            }
            
        } // if ($username)
        
    }

?>