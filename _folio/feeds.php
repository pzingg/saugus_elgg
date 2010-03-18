<?php
/**
* View feeds in a variety of different formats.
*       Note that this file uses rewritten urls set in .htaccess
* Both types & tags use a pattern to break apart argument into an array.
* Delimited on + & -, treats all as a special case.  
*       EG: for all but files, pattern would be all-files.  
*       EG: for blog, folio, files, blog+folio+file.  
*       EG: for all tags except personal and education, all-personal-education
*
* @package folio
* @param string $user The user/community we're viewing. 
* @param string $unpwdhash The MD5 Hashed user+password to authenticate an external reader. NOTE: NOT YET IMPLEMENTED
* @param string $purpose Feed or Activity -- who is the page being generated for...
*       If feed, then showing a person's actions to the world.
*       If activity, then showing things in the world happening for a single user.
* @param string $format The way that we're looking at the data, aka RSS / HTML / ...
* @param string $types The types of things we're looking at (see pattern) 
* @param string $tags The types of things we're looking at (see pattern)   NOTE: NOT YET IMPLEMENTED
* @param int $page The page of results we're looking at
*/
    define("context", "rss");
	$itemsperpage = 10;
	
    // Run includes
    require_once("../includes.php");
	require_once("../mod/folio/control/feeds.php");
	
	run("profile:init");
    run("friends:init");
    run("folio:init");
    global $metatags;
    
    // Create major variables.
    $readerhash; $username; $format; $purpose; $types; $types_avoid; $page; $user_ident; 
    // The ident of the reader, used for validation reasons.
    $reader_ident;
    // Create variables used to build html
    $from=0; $page=0; $results; $resultcount;
    
    // Load data from the GET variables & return.
    loadData( &$readerhash, &$user_ident, &$username, &$format, &$purpose, &$types, &$types_avoid, &$page);
    
    $page_owner = $user_ident;
    
    // Run validation check.  if fail, -1 is returned.
    if ( $readerhash != '' ) {
        $reader_ident = folio_decodehash( $readerhash );
        if ( $reader_ident == -1 ) {
            error("Error, unable to validate your $readerhash value. Please log in and make sure you have the correct value.");
        }
    } elseif ( isloggedin() ) {
        $reader_ident = $_SESSION['userid'];
    } else {
        $reader_ident = -1;
    }
    
    // Build where condition
    $where = buildWhere( $purpose, $reader_ident, $user_ident, $types, $types_avoid );

	// Get Data
	getData( $where, $from, $page, &$results, &$resultcount);
    
    // Setup Metatags for RSS Discovery
    if ( isloggedin() ) {
        $metatags .= "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS\" href=\"" . 
            url . $username . '/' . $purpose . '/rss/' . folio_createhash( $_SESSION['userid'] ) . "\"/>";
    } else {
        $metatags .= "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS\" href=\"" . 
            url . $username . '/' . $purpose . "/rss/\"/>";

    } 
    
    // Print results to the screen.
    echo returnHTML( $purpose, $format, $results, $resultcount, $page, $username) ;
    

//////////////////////////////////////////////////// 
// Major Functions
//////////////////////////////////////////////////// 

/**
* Read in & validate information
**/
    function loadData( &$readerhash, &$user_ident, &$username, &$format, &$purpose, &$types, &$types_avoid, &$page) {

    	// Retrieve passed variables 
        $readerhash = optional_param('key', '');
        
    	$username = folio_clean( required_param('user') );
        $format = required_param('format');
    	$purpose = required_param('purpose');
        $types = str_replace( ' ', '+', required_param('types'));
    	$tags = str_replace( ' ', '+', required_param('tags'));
    	$page = intval( optional_param('page', 1, PARAM_INT) );
    		
        // Setup & Validate Owner
    	$user_ident = run('users:name_to_id', $username);
    	
    	if ( !$user_ident ) {
    		// Nothing returned by the run command, not a valid user.  
    		error( 'Sorry, but "' . $username . '" is not a valid username in this system.'); die();
    	} 
        
        // PURPOSE
    	if ( ! ($purpose == 'activity' || $purpose == 'subscribe' ) ) {
    		error('Invalid purpose ' . $purpose . ' passed to feeds.php'); die();
        }

    	// FORMAT
    	if ( ! ( $format == 'html' || $format == 'rss' ) ) {
    		error('Invalid format ' . $format . ' passed to feeds.php'); die();
    	}
    	
    	// TYPES & TAGS

        //  Convert from a string with tag+tag-tag to an array.
    	folio_feed_parselist( $tags, &$tags, &$tags_avoid  );
    	folio_feed_parselist( $types, &$types, &$types_avoid  );

    	// Clean all of the tags and convert arrays back to a string with commas in it delimiting the values.
    	$tags = rss_getitems_where( $tags );
    	$tags_avoid = rss_getitems_where( $tags_avoid );
    	$types = rss_getitems_where( $types );
    	$types_avoid = rss_getitems_where( $types_avoid);
       
    	// SET CURRENT PAGE
    	if ($page < 1) { $page = 1; }
 
    }

    
/**
* Based off of the incoming parameters, build a SQL where condition
**/
    function buildWhere( $purpose, $reader_ident, $user_ident, $types, $types_avoid ) {
        global $CFG;
        
    	if ( $purpose == 'activity' ) {
            // FIND FRIENDS AND JOINED COMMUNITIES.
            
            // Find key values for friends.
            $friendlist = array();
            $friends = recordset_to_array(get_recordset_sql( 
                "SELECT DISTINCT friend as i, friend FROM " . $CFG->prefix . "friends where owner = $user_ident"));
            
            if ( $friends ) {
                foreach ( $friends as $friend ) {
                    $friendlist[] = $friend->friend;
                }
            }
            
            // Find key values for owned communities
            $friends = recordset_to_array(get_recordset_sql( 
                "SELECT DISTINCT ident as i, ident FROM " . $CFG->prefix . "users where owner = $user_ident"));
            
            if ( $friends ) {
                foreach ( $friends as $friend ) {
                    $friendlist[] = $friend->ident;
                }
            }
                
            // Transform array into a where clause.
            if ( count( $friendlist ) > 0 ) {
            
                $friendlist = implode( ',', $friendlist);
                $where = 'owner_ident in (' . $friendlist . ')';
                
            } else {
                // If a person has no friends or communities, then they have no activity.
                $where = ' false ';
            }
            
    	} elseif ( $purpose == 'subscribe' ) {
            // Subscribe to a person's feed.
            $where = "owner_ident = $user_ident";
        } else { die('unknown purpose passed to feeds.php'); }
        
        // Add permissions
        $where .= ' AND access in ("' . implode( '","', rss_permissionlist( $reader_ident ) ) . '")';
        
        // Add type filter
        $where .= rss_buildwhere( 'type', $types, $types_avoid);

        return $where;
    }

/**
* Get data
**/
    function getData( $where, $from, $page, &$results, &$resultcount) {
        global $itemsperpage;
        
    	// Get count of results.
    	$resultcount = rss_getcount( $where );
    	
    	// Build $from variable
    	$from = ($page-1) * $itemsperpage;
    	if ( $from > $resultcount ) { 
    		error('Invalid page number ' . $page . ' passed to function folio_control_htmlfeed');
    		die();
    	}

    	// Get individual results
    	if ( $resultcount > 0 ) {
            $results = rss_getitems( $where, $from, $itemsperpage );
        } else {
            $results = false;
        }  
    }
    
/**
* Build presentation data
**/    
    function returnHTML( $purpose, $format, $results, $resultcount, $page, $username ) {
        $url = url;
    
            // PURPOSE
    	if ( $purpose == 'activity' ) {
            $pagetitle = 'Recent Activity';
    	} elseif ( $purpose == 'subscribe' ) {
            $pagetitle = 'Recent Changes';
        } else {
    		error('Invalid purpose ' . $purpose . ' passed to feeds.php');
    		die();
        }

    
    	switch( $format ) {
    		case 'html':

    			$body = folio_control_htmlfeed( $results, $resultcount, $page, $username ); 
    			
    			// Transfer into template & write.
    		    templates_page_setup();
                
                if ( isloggedin() ) {
                    $rsskey = folio_createhash( $_SESSION['userid'] . '/' );
                } else {
                    $rsskey = '';
                }
                $types = str_replace( ' ', '+', required_param('types'));
    						
    		    $body = templates_draw(array(
                        'context' => 'contentholder',
                        'title' => "<a href='$url{$username}/{$purpose}/rss/{$types}/{$rsskey}'><img border=0 src='{$url}_templates/icons/rss.png' /></a> $username :: $pagetitle ",
                        'body' => $body
                    )
                    );

    			return templates_page_draw(array(
                        $pagetitle, $body
                    ));
    				
    			break;
    		case 'rss':
    			$body = folio_control_rssfeed( $results, $resultcount, $username ); 
    			
    			header("Pragma: public");
    			header("Cache-Control: public"); 
    			header('Expires: ' . gmdate("D, d M Y H:i:s", (time()+3600)) . " GMT");
    			
    			$etag = md5($body);
    			header('ETag: "' . $etag . '"');
    			
    			header("Content-Length: " . strlen($body));
    			
    			header("Content-type: text/xml; charset=utf-8");

    			return $body;
    			
    			break;
    		default:
    			error('Invalid format passed to feeds.php');
    			break;
    	}
    }
    
//////////////////////////////////////////////////// 
// Helper Functions
//////////////////////////////////////////////////// 


	function rss_getitems_where( $array ) {
		// filter invalids
		array_walk(&$array, 'folio_clean_array');
		// Combine
        return $array;
		//return implode(",", $array);
	}	
	
    function rss_buildwhere( $fieldname, $good, $bad ) {
        $html = '';
        // add in good items
        // Assume that there is at least one.
        if ( count( $good ) > 0 & !in_array( 'all', $good ) ) {
            $html .= ' (';
            foreach ( $good as $i ) {
                $html .= " $fieldname = '$i' OR ";
            }
            // remove trailing OR
            
            $html = substr( $html, 0, strlen( $html ) - 3 ) . ' ) ';
        }

        // add in bad items
        if ( count( $bad ) > 0 ) {
            if ( strlen( $html ) > 0 ) { $html .= ' AND '; }
            
            $html .= ' (';
            foreach ( $bad as $i ) {
                $html .= " $fieldname <> '$i' AND ";
            }
            // remove trailing OR
            $html = substr( $html, 0, strlen( $html ) - 4 ) . ' ) ';
        }
        
        if ( strlen( trim( $html ) ) > 0 ) { $html = ' AND ' . $html; }
        return $html;

    }

	
?>