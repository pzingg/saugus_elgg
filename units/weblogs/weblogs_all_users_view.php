<?php

// View a weblog

// Get the current profile ID

global $CFG;
global $page_owner;
global $db;

// If the weblog offset hasn't been set, it's 0
$weblog_offset = optional_param('weblog_offset',0,PARAM_INT);
$view_filter = optional_param('filter','');
$view_filter_value = trim(optional_param('filtervalue'));

$where1 = run("users:access_level_sql_where",$_SESSION['userid']);

// if (!isset($_SESSION['friends_posts_cache']) || (time() - $_SESSION['friends_posts_cache']->created > 60)) {
// $_SESSION['friends_posts_cache']->created = time();
// $_SESSION['friends_posts_cache']->data = get_records_select('weblog_posts','('.$where1. ') AND ('.$where2.')',null,'posted DESC','*',$weblog_offset,25);
// }
// $posts = $_SESSION['friends_posts_cache']->data;

$removefilter = ' (<a href="' . url . '_weblog/everyone.php?weblog_offset=' . $weblog_offset . '">' . gettext('Remove filter') . '</a>)';

switch ($view_filter) {
    case "people":
        $where1 = '(' . $where1 . ') AND owner = weblog';
        $run_result .= '<p>' . gettext('Filtered: Showing personal blog posts') . $removefilter . '</p>';
        break;
    case "communities":
        $where1 = '(' . $where1 . ') AND owner != weblog';
        $run_result .= '<p>' . gettext('Filtered: Showing community blog posts') . $removefilter . '</p>';
        break;
    case "commented":
        //a join would be better, but doesn't work with users:access_level_sql_where anyway - sven
        $where1 = '(' . $where1 . ') AND (SELECT COUNT(ident) FROM ' . $CFG->prefix . 'weblog_comments WHERE post_id = ' . $CFG->prefix . 'weblog_posts.ident) > 0';
        $run_result .= '<p>' . gettext('Filtered: Showing posts with comments') . $removefilter . '</p>';        
        break;
    case "uncommented":
        $where1 = '(' . $where1 . ') AND (SELECT COUNT(ident) FROM ' . $CFG->prefix . 'weblog_comments WHERE post_id = ' . $CFG->prefix . 'weblog_posts.ident) = 0';
        $run_result .= '<p>' . gettext('Filtered: Showing posts with no comments') . $removefilter . '</p>';        
        break;
    case "date":
        //expect a YYYYMMDD value
        $view_filter_value = (int) $view_filter_value;
        $view_filter = '';
        if (strlen($view_filter_value) == 8) {
            $year = substr($view_filter_value, 0, 4);
            $month = substr($view_filter_value, 4, 2);
            $day = substr($view_filter_value, 6, 2);
            $start = gmmktime(0,0,0, $month, $day, $year);
            $end = gmmktime(0,0,0, $month, $day + 1, $year);
            if ($start && $end) {
                $where1 = '(' . $where1 . ') AND (posted BETWEEN ' . $start . ' AND ' . $end . ')';
                $nicedate = gmstrftime("%B %d, %Y", $start);
                $run_result .= '<p>' . gettext('Filtered: Showing posts from ') . $nicedate . $removefilter . '</p>';
                $view_filter = 'date';
            }
        }
        break;
    case "tag":
        if ($view_filter_value) {
            $sql_filter_value = $db->qstr($view_filter_value); // adodb's escaping + quote-surrounding function
            $html_filter_value = htmlspecialchars($view_filter_value);
            $where1 = '(' . $where1 . ') AND ident IN (SELECT ref FROM ' . $CFG->prefix . 'tags WHERE tag = ' . $sql_filter_value . ' AND tagtype = "weblog")';
            $run_result .= '<p>' . gettext('Filtered: Showing posts tagged with ') . '"' . $html_filter_value . '"' . $removefilter . '</p>';
        }
        break;
    default:
        $view_filter = '';
        break;
}

$posts = get_records_select('weblog_posts', $where1, null, 'posted DESC', '*', $weblog_offset, 25);
$numberofposts = count_records_select('weblog_posts', $where1);

if (!empty($posts)) {
            
    $lasttime = "";
    
    foreach($posts as $post) {
        
        $time = gmstrftime("%B %d, %Y",$post->posted);
        if ($time != $lasttime) {
            $run_result .= "<h2 class=\"weblog_dateheader\">$time</h2>\n";
            $lasttime = $time;
        }
        
        $run_result .= run("weblogs:posts:view",$post);
        
    }
    
    $weblog_name = htmlspecialchars(optional_param('weblog_name'), ENT_COMPAT, 'utf-8');
    
    if ($view_filter) {
        $filterurl = '&amp;filter=' . urlencode($view_filter);
        if ($view_filter_value) {
            $filterurl .= '&amp;filtervalue=' . urlencode($view_filter_value);
        }
    } else {
        $filterurl = '';
    }
    
    if ($numberofposts - ($weblog_offset + 25) > 0) {
        $display_weblog_offset = $weblog_offset + 25;
        $back = gettext("Back"); // gettext variable
        $run_result .= <<< END
                
                <a href="{$CFG->wwwroot}_weblog/everyone.php?weblog_offset={$display_weblog_offset}{$filterurl}">&lt;&lt;  $back</a>
                <!-- <form action="" method="post" style="display:inline">
                    <input type="submit" value="&lt;&lt; Previous 25" />
                    <input type="hidden" name="weblog_offset" value="{$display_weblog_offset}" />
                </form> -->
                
END;
    }
    if ($weblog_offset > 0) {
        $display_weblog_offset = $weblog_offset - 25;
        if ($display_weblog_offset < 0) {
            $display_weblog_offset = 0;
        }
        $next = gettext("Next"); // gettext variable
        $run_result .= <<< END
                
                <a href="{$CFG->wwwroot}_weblog/everyone.php?weblog_offset={$display_weblog_offset}{$filterurl}">$next &gt;&gt;</a>
                <!-- <form action="" method="post" style="display:inline">
                    <input type="submit" value="Next 25 &gt;&gt;" />
                    <input type="hidden" name="weblog_offset" value="{$display_weblog_offset}" />
                </form> -->
                
END;
    }
    
}

?>