<?php
	global $CFG;
	
    require_once(dirname(__FILE__)."/includes.php");
    templates_page_setup();    
    if (logged_on) {
        $body = run("content:mainindex");
    } else {
    	$sitename = sitename;
    	$body = "<h5>" . gettext("Welcome") . "</h5>";
		$body .= "<p>This is $sitename, a learning landscape. Our most recent posters are...<br />";
		
		$result = get_records_sql('SELECT u.icon,u.name,u.username,u.ident,w.posted from '.$CFG->prefix.'weblog_posts w, '.$CFG->prefix.'users u where u.ident=w.weblog and w.access = ? order by w.posted desc limit 25',array('PUBLIC'));
    
    $body .= <<< END
    <div class="networktable">
    <table>
        <tr>
END;
    $i = 1;
    $keys[] = -2;
    if (!empty($result)) {
        foreach($result as $key => $info) {
          if (! in_array($info->ident,$keys)) {
            $keys[] = $info->ident;
            $w = 100;
            
            // $friends_name = htmlspecialchars(stripslashes($info->name), ENT_COMPAT, 'utf-8');
            $friends_name = $info->name;
            $info->icon = run("icons:get",$info->ident);
            //$friends_menu = run("users:infobox:menu",array($info->ident));
            $body .= <<< END
        <td width="100" align="center">
            <p>
            <a href="{$CFG->wwwroot}{$info->username}/weblog">
            <img src="{$CFG->wwwroot}{$info->username}/icons/{$info->icon}/w/{$w}" alt="{$friends_name}" border="0" /></a><br />
            <span class="userdetails">
                {$friends_name}
            </span>
            </p>
        </td>
END;
            if ($i % 5 == 0) {
                $body .= "</tr><tr>";
            }
            $i++;
            if ($i > 5) break;
          }
        }
    } 
    $body .= <<< END
    </tr>
    </table>
    </div>
END;

	$body .= sprintf(gettext("<a href=\"%s\">Find others</a> with similar interests and goals."), url . "search/tags.php") . "<br /><br />";
	$body .= "<p align=\"center\"><a href=\"_weblog/everyone.php\"><img src=\"_templates/default_images/view_blog.gif\" border=\"0\"></a></p>";
    }
    
    echo templates_page_draw( array(
                    sitename,
                    templates_draw(array(
                                                    'body' => $body,
                                                    'title' => gettext("Main Index"),
                                                    'context' => 'contentholder'
                                                )
                                                )
            )
            );
            
?>
