<?php
/**
* This file provides individual controls & formatting for the feeds.php file.
*
* @package folio
**/

///////////////////////////////////////////////////////////////////////////////////////////
//  SHARED FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////
  

/**
*Parse out a list of variables into a + and a - array.
*	ex: 
*		IN:   "sam+bob-jill+dave" 
*		OUT: plus(array) = ['sam', 'bob', 'dave'] and minus(array) = ['jill']
*
* @param $inarray string The incoming string list of arguments.
* @param OUTPUT $plus array Items that had a + by them
* @param OUTPUT $minus array Items that had a - by them.
**/
function folio_feed_parselist( $instring, &$plus, &$minus  ) {
	
	// 	Break plus into array 
	$plus = explode( "+", $instring );

	$minus = '';
	array_walk(&$plus, 'folio_feed_parselist_removenegtags', &$minus);
	
	// 	Remove leading - character (if present) as the function returns a string, not an array.
	if ( substr( $minus, 0, 1 ) == '-' ) { 
		$minus = substr( $minus, 1, strlen( $minus ) -1 ) ;
	}
	
	// 	Break minus into array 
    if ( strlen( trim( $minus ) ) > 0 ) {
        $minus = explode( "-", $minus );
    } else {
        $minus = array();
    }

}
// Helper function for negative tags
//     Pulls labels with a - out from the array and returns them as a single string.
function folio_feed_parselist_removenegtags(&$item, $key, &$avoid) {
	if ( ! ( ($i = strpos( $item, '-')) === false) ) {
		$avoid .= substr( $item, $i );
		$item = substr( $item, 0, $i );
	}
}	

///////////////////////////////////////////////////////////////////////////////////////////
//  HTML VIEW
///////////////////////////////////////////////////////////////////////////////////////////

/**
* Return the html code for viewing the passed items.
*
**/
function folio_control_htmlfeed( $items, $itemcount, $page, $username ) {
	global $profile_id;
	global $page_owner;
	
	$perpage = 10;
	
	// Build $from variable
	$from = ($page-1) * $perpage;
	if ( $from > $itemcount ) { 
		error('Invalid page number ' . $page . ' passed to function folio_control_htmlfeed');
		die();
	}

	// Disabled code, not needed since I don't use the javascript expand property for the results.	
	//	If I ever use again, be sure to uncomment the js stuff below belonging to this function.
	//	$keys = '';	
	//	array_walk( $results, 'retrieve_rsskeys', &$keys);
	//	build_js(&$keys) . 
	
	// Build top of page
	$body = folio_control_htmlfeed_header ($page, ceil( floatval( $itemcount) / $perpage ) );

	// Start list numbering (increment $from, as it's zero-based)
	$body .= "<ol start='" . ( $from + 1 ) . "'>";
	
	// Add individual items. (false if empty)
	if ( $items ) {
        array_walk( $items, 'folio_control_htmlfeed_item', &$body );
    } else {
        $body = 'No items to show.';
    }
	
	$body .= '</table></ol>';
		
	return $body;

	}

// Format results
function folio_control_htmlfeed_item( $result, $key, &$html ) {
    $url = url;
    $icon = run("icons:get", $result->user_ident);
    $date = gmdate("D M d",intval($result->created));
    $type = str_replace( 'weblog', 'blog', str_replace( 'page', 'wiki page', str_replace( '_', ' ', $result->type )));
   
    $html .= <<< END
        <tr>
            <td width="70" valign="top">
                <a href='{$url}{$result->user_username}'>
                    <img alt="" src="{$url}{$result->user_username}/icons/{$icon}/h/67/w/67" border="0"/>
                </a>
            </td>
            <td valign='top'>
                <b><a href='{$result->link}'>{$result->title}</a></b><br/>
                <a href='{$url}{$result->owner_username}'>{$result->owner_username}</a> | $type | $date<br/>
                $result->body <i></i>
            </td>
        </tr>
END;


		// Saved javascript expand/collaspe code: 
		//	javascript: switchMenu('rssitem_{$key}');
	}

// Build pagination control for top of page.
function folio_control_htmlfeed_header ($page, $pages ) {
		// Don't build header if there's only a single page.
		if ($pages == 1) { return '<table><br/>'; }
		
		$result = "<table border = 0 width=100% ><tr><td width=30% align=right>";

		if ( $page < $pages ) {
				$result .= "<a href='" . ($page + 1) . "'>&lt;&lt; Back</a> ";
		}

		$result .= "</td><td width=40% align=center><b> Page $page of $pages </b><td width=30% align=left>";
		
		if ( $page > 1 ) {
				$result .= "<a href='" . ($page - 1) . "'>Forward &gt;&gt;</a> ";
		}
		else {
			$result .= "&nbsp;";
		}
		
		return $result . "</td></tr></table><table><br/><br/>" ;
	}	
	
///////////////////////////////////////////////////////////////////////////////////////////
//  UNUSED JAVASCRIPT
///////////////////////////////////////////////////////////////////////////////////////////

	
	/*
* Commented out, as folio_control_htmlfeed was modified to not have the 
*	individual entries commented out.  Save for reference.

	function build_js( $keys ) {

		return <<< END
		<script type="text/javascript">
			// Expand & Collaspe JS Code
			// Original version posted online at:
			// http://www.dustindiaz.com/dhtml-expand-and-collapse-div-menu/
		<!--
			function switchMenu(obj) {
				var el = document.getElementById(obj);
				if ( el.style.display != 'none' ) {
					el.style.display = 'none';
				} else {
					el.style.display = '';
				}
			}

			function $() {
				var elements = new Array();
				for (var i = 0; i < arguments.length; i++) {
					var element = arguments[i];
					if (typeof element == 'string')
						element = document.getElementById(element);
					if (arguments.length == 1)
						return element;
					elements.push(element);
				}
				return elements;
			}

			function displayElements(objs, displaystyle) {
				var i;
				for (i=0;i<objs.length;i++ ) {
					objs[i].style.display = displaystyle;
				}
			}
			
			function openItems() {
				displayElements($({$keys}), '');
			}
			function closeItems() {
				displayElements($({$keys}), 'none');
			}

			addEvent(window,'load',pageLoad);
		-->
		</script>	
END;

	}


	// Format results
	function retrieve_rsskeys( $result, $key, &$html ) {
		if ( strlen($html) > 0 ) {
			$html .= ",'rssitem_{$key}' ";
		} else {
			$html .= "'rssitem_{$key}'";
		}

	}	
*/


///////////////////////////////////////////////////////////////////////////////////////////
//  RSS
///////////////////////////////////////////////////////////////////////////////////////////


/**
* Return the html code for viewing the passed items.
*
**/
function folio_control_rssfeed( $items, $itemcount, $username ) {

	$perpage = 2;
	$url = url;
//<xml-stylesheet type="text/xsl" href="{$url}{$username}/weblog/rss/rssstyles.xsl">
	// Build Header
	$body = <<< END

<rss version='2.0'   xmlns:dc='http://purl.org/dc/elements/1.1/'>
<channel xml:base='{$url}{$username}/weblog/'>
	<title><![CDATA[{$username} : RSS Feed]]></title>
	<description><![CDATA[The RSS Feed for {$username} using the Elgg software]]></description>
	<generator>Elgg</generator>
	<link>{$url}{$username}/page/</link>        
END;

	// Add individual items.
	array_walk( $items, 'folio_control_rssfeed_item', &$body );
	
	// Build footer
	$body .= "    </channel></rss>";

    return $body;
}

// Format results
function folio_control_rssfeed_item( $result, $key, &$html ) {
		$url = url;
		$date = gmdate("m/d/y H:i",intval($result->created));
		
		$html .= <<< END
		
        <item>
            <title><![CDATA[{$result->title}]]></title>
            <link>{$result->link}</link>
            <guid isPermaLink="true">{$result->link}</guid>
            <pubDate>{$date}</pubDate>
            <description><![CDATA[{$result->body}]]></description>
        </item>
END;
	}

?>