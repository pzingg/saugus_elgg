<?php
global $CFG;
$body = '';
    if (logged_on) {
        
        $body .= "<p>". gettext("Feeds are information streams from other sites. You will often see a link to an 'RSS' feed while browsing; enter the link address into the 'add feed' box at the bottom of the page to read that information from within your learning landscape.") . "</p>";
        $body .= "<p>". gettext("This is a list of the feeds with the most subscribers.") . "</p>";
        
        if ($feed_subscriptions = get_records_sql('SELECT f.ident,f.name,f.last_updated,f.siteurl, COUNT(fs.user_id) AS numsubs
                                                   FROM '.$CFG->prefix.'feed_subscriptions fs JOIN '.$CFG->prefix.'feeds f ON f.ident = fs.feed_id
                                                   GROUP BY f.ident,f.name,f.last_updated,f.siteurl ORDER BY numsubs DESC, f.name ASC LIMIT 25')) {
            $body .= templates_draw( array(
                    'context' => 'adminTable',
                    'name' => "<b>" . gettext("Last updated") . "</b>",
                    'column1' => "<b>" . gettext("Link to site") . "</b>",
                    'column2' => "&nbsp;"
                )
                );
            
            foreach($feed_subscriptions as $feed) {
                $name = ($feed->name) ? stripslashes($feed->name) : gettext("Unnamed Feed");
                $name = "<a href=\"".$feed->siteurl."\">" . $name . "</a>";
                $column2 = "<a href=\"".url."_rss/individual.php?feed=".$feed->ident."\">". gettext("View posts") . "</a> | ";
                
                $subtest = run("rss:subscribed", $feed->ident);
                if ($subtest) {
                    $column2 .= "<a href=\"".url."_rss/subscriptions.php?action=unsubscribe&amp;feed=".$feed->ident."\" onclick=\"return confirm('".gettext("Are you sure you want to unsubscribe from this feed?")."')\">" . gettext("Unsubscribe") . "</a>";
                } else {
                    $column2 .= "<a href=\"".url."_rss/subscriptions.php?action=subscribe&amp;feed=".$feed->ident."\" onclick=\"return confirm('".gettext("Are you sure you want to subscribe to this feed?")."')\">" . gettext("Subscribe") . "</a>";
                }
                
                $column2 .= "<br />" . $feed->numsubs . " " . gettext("Subscribers") . "";
                
                $body .= templates_draw( array(
                        'context' => 'adminTable',
                        'name' => strftime("%B %d %Y, %H:%M",$feed->last_updated),
                        'column1' => $name,
                        'column2' => $column2
                    )
                    );
                    
            }
            
        } else {
            $body .= "<p>" . gettext("No-one has subscribed to any feeds.") . "</p>";
        }
        $body .= "<p>". gettext("To subscribe to a new feed, enter its address below:") . "</p>";
        $body .= "<form action=\"subscriptions.php\" method=\"post\">";
        $body .= templates_draw( array(
                'context' => 'adminTable',
                'name' => "&nbsp;",
                'column1' => "<input type=\"text\" name=\"url\" value=\"http://\" style=\"width: 100%\" />",
                'column2' => "<input type=\"submit\" value=\"".gettext("Subscribe") . "\" />"
            )
            );
        $body .= "<input type=\"hidden\" name=\"action\" value=\"subscribe-new\" /></form>";
        
        $run_result .= $body;
        
    }

?>

