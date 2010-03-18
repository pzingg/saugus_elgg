<?php
/**
* The sidebar menu for this mod.  Lib.php page_setup function will reset all of the sidebars
*	so that this replaces both the blog sidebar & the file sidebar.
*
* @package folio
* @param string $username The url-usable name of the page's owner.  Will find if not set. OPTIONAL
* @param int $page_owner The ident for the page's owner.
**/
global $CFG;

if ( !isset ( $FOLIO_CFG) ) {
    // Note to self: do not change to require_once, as this config file needs to be forced to load.
    require $CFG->dirroot . 'mod/folio/config.php';
}
global $page_owner;
global $username;
global $profile_id;

if ( !isset( $username ) ) {
    if ( isset( $page_owner ) ) {
        $username = folio_getUsername( $page_owner ); 
    } elseif ( isset( $profile_id ) ) {
        $username = folio_getUsername( $profile_id ); 
    }


}

// Name is not guaranteed to be linkable, unlike the username.
$displayname = run("profile:display:name", $page_owner);

$url = url;
$sitename = sitename;

// irrelevant in new config - JK
// Add Recent Activity if we're logged in & looking at our own pages. 
//if (logged_on && $page_owner == $_SESSION['userid']) {
//    
//    $title = gettext("Recent Activity");
//
//	$run_result .= "<li id=\"recent_activity\">" .
//		templates_draw(array(
//						'context' => 'sidebarholder',
//						'title' => $title,
//						'body' => "<ul><li><a href=\"{$url}_activity/\">" . 
//							gettext("View your activity") . "</a></li></ul>",
//						)
//				  );
//	$run_result .= "</li>";
//
//} 

// Add chatbox link.  Depends upon the chatbox functionality being loaded.
if ( $FOLIO_CFG->chatbox_installed == 'Y') {
    $run_result .= "<li id=\"oneline_chat\">" .
        templates_draw(array(
                        'context' => 'sidebarholder',
                        'title' => 'Online Chat',
                        'body' => "<ul><li><a href=\"{$url}_chatbox/chat.php\">" . 
                            gettext("Chat online") . "</a></li></ul>",
                        )
                  );
    $run_result .= "</li>";
}

// Begin building the main links to this person's content.
if (isset($page_owner) && $page_owner != -1 && $page_owner != 0) {

	$title = gettext("Content");

	// Add profile Entry
	$body = <<< END
		<ul><li><a href="$url{$username}/">Profile</a></li>
END;
	
	// Add Blog Entry
	if (run("users:type:get",$page_owner) == "person") {
		$personalWeblog = gettext("Blog");
		$body .= <<< END
			<li><a href="$url{$username}/weblog/">$personalWeblog</a>
			(<a href="$url{$username}/weblog/rss">RSS</a>)</li>
END;
	} else if (run("users:type:get",$page_owner) == "community") {
		$communityWeblog = gettext("Community blog");
		$body .= <<< END
			<li><a href="$url{$username}/weblog/">$communityWeblog</a> 
			(<a href="$url{$username}/weblog/rss">RSS</a>)</li>
END;
	}

    $body .= "<li><a href=\"$url{$username}/files/\">Files</a></li>";
    
    if ((get_field("users","owner","ident",$page_owner) == -1) || (run("users:type:get",$page_owner) == "community")) { //prevent Wiki Pages option for owned users, for now
		// Add wiki entry
	    if ( $FOLIO_CFG->wiki_menu_dropdown == 'Y' ) {
	        // Include the drop-down menu
	        
	        if ( isloggedin() ) {
	            $rsskey = folio_createhash( $_SESSION['userid'] . '/');
	        } else {
	            $rsskey = '';
	        }
	        
	    	$body .= <<< END
    		<li><a id='trigger2' class='trigger' href="$url{$username}/page/">Wiki Pages</a>		
    			<style type='text/css'>
    			.menu {
    			  position:absolute;
    			  visibility:hidden;
    			  overflow:hidden;
    			  z-index:1;
    			  margin:10px;
    			  padding:10px;
    			  background: white; 
    			}
    			.userinfomenu_box{
    			   background-color: #F9F9F9;
    			   color:#000;
    			   border:1px;
    			   border-style:solid;
    			   border-color:black;
    			   margin:5px;
    			   padding:5px;
    			 }
    			</style>
    		<script type='text/javascript' src='{$url}mod/folio/control/crossbrowsercom/x_core.js'></script>
    		<script type='text/javascript' src='{$url}mod/folio/control/crossbrowsercom/x_event.js'></script>
    		<script type='text/javascript'>

    		function menuInit() {
    			new xMenu1('trigger2', 'menu2', 10, 'mouseover');
    		}
    		function xMenu1(triggerId, menuId, mouseMargin, openEvent)
    		{
    		  var isOpen = false;
    		  var trg = xGetElementById(triggerId);
    		  var mnu = xGetElementById(menuId);
    		  if (trg && mnu) {
    		    xAddEventListener(trg, openEvent, onOpen, false);
    		  }
    		  function onOpen()
    		  {
    		    if (!isOpen) {
    		      xMoveTo(mnu, xPageX(trg), xPageY(trg) + xHeight(trg));
    		      xShow(mnu);
    		      xAddEventListener(document, 'mousemove', onMousemove, false);
    		      isOpen = true;
    		    }
    		  }
    		  function onMousemove(ev)
    		  {
    		    var e = new xEvent(ev);
    		    if (!xHasPoint(mnu, e.pageX, e.pageY, -mouseMargin) &&
    		        !xHasPoint(trg, e.pageX, e.pageY, -mouseMargin))
    		    {
    		      xHide(mnu);
    		      xRemoveEventListener(document, 'mousemove', onMousemove, false);
    		      isOpen = false;
    		    }
    		  }
    		} // end xMenu1
    		</script>
    		
    		<ul id='menu2' class='menu'>
    		<div class="userinfomenu_box"><ul>
    		<li><a href='$url{$username}/page/'>Homepage</a>
    		<li><a href='$url{$username}/subscribe/html/page+page_comment/'>Recent&nbsp;Changes</a>
    		<li><a href='$url{$username}/subscribe/rss/page+page_comment/{$rsskey}'><img border=0 src="{$url}_templates/icons/rss.png" onload="javascript:menuInit()" />&nbsp;Subscribe</a>
    		</ul></div></ul>
    		</li>
END;
	    } else {
	        // Simple menu style
	        $body .= "<li><a href=\"$url{$username}/page/\">Wiki Pages</a></li>";
	    }
	}
	// Add file entry
	$body .= <<< END
		
	</ul>

END;
} else {
	// this happesn during http://elgg.net/ addressing.
}

	// Build & Display to the screen.
	$run_result .= "<li id=\"sidebar_content\">";
	$run_result .= templates_draw(array(
										'context' => 'sidebarholder',
										'title' => $title,
										'body' => $body,
										)
								  );


?>