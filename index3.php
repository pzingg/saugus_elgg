<?php
	global $CFG;
	
    require_once(dirname(__FILE__)."/includes.php");
    templates_page_setup();    
    if (logged_on) {
        $body = run("content:mainindex");
    } else {
    	$sitename = sitename;
    	$body = "<b>" . gettext("Welcome") . "</b>";
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
        <td>
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

	$body .= "<p align=\"center\"><a href=\"_weblog/everyone.php\"><img src=\"/images/view_blog.gif\" border=\"0\"></a></p><br/>";

	$body .= "<b>Some things we are interested in:</b><br/><p style=\"background: #EEE url(https://webapps.saugus.k12.ca.us/community4students/_templates/GreenTrack/blockquote.png) no-repeat bottom left;padding:5px;\"";

$searchline = "(" . run("users:access_level_sql_where",$USER->ident) . ")";

if ($tags = get_records_sql('SELECT DISTINCT tag,count(ident) AS number, '.$db->random.' AS rand 
                             FROM '.$CFG->prefix."tags WHERE access = ?
                             GROUP BY tag having number > 1 order by rand limit 200",array('PUBLIC'))) {
    $max = 0;
    foreach($tags as $tag) {
        if ($tag->number > $max) {
            $max = $tag->number;
        }
    }
    
    $tag_count = 0;
    foreach($tags as $tag) {
        
        if ($max > 1) {
            $size = round((log($tag->number) / log($max)) * 300) + 100;
        } else {
            $size = 100;
        }
	$size = round($size / 2);
        
        $tag->tag = stripslashes($tag->tag);
        $body .= "<a href=\"".url."tag/".urlencode(htmlspecialchars(strtolower(($tag->tag)), ENT_COMPAT, 'utf-8'))."\" style=\"font-size: $size%\" title=\"".htmlspecialchars($tag->tag, ENT_COMPAT, 'utf-8')." (" .$tag->number. ")\">";
        $body .= $tag->tag . "</a>";
        if ($tag_count < sizeof($tags) - 1) {
            $body .= ", ";
        }
        $tag_count++;
    }
    
}   
	$body .= "</p>";

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
