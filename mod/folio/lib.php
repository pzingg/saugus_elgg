<?php
/**
* Library of commonly-used functions
* @package folio
**/

require_once $CFG->dirroot . 'mod/folio/config.php';
/**
* Get the current version of this add-in.  This will always be a numeric value to allow for < or > comparisons.
**/
function folio_version() {
	return 0.5;
}

/**
* Create the menu links used by the folio add-in, as well as setting the commands for the info / navigation menu
* on the side of the page.
*/
function folio_pagesetup() {
    global $profile_id;
	global $page_owner;
    global $PAGE;
    global $CFG;
    global $FOLIO_CFG;
    global $USER;
    
	global $metatags;
	global $function;
	// These are defined by view.php, and are needed to properly setup the edit & history links for the submenu.
    global $page_ident;
	global $page_title;
	global $username; // this is who we're looking at, not the logged in user.

	$currentusername = $_SESSION['username'];
	
	if ($CFG->folio) {
	
		// -----------------------------------------------
		// SETUP MENU
		//
	    // Main Menu
	    if (isloggedin() && $USER->owner == -1) { //no "Your Pages" for owned users
	    	if (defined('context')) {
				if ( substr(context, 0, 5) == "folio" & $username == $currentusername) {
		    
			        // Show selected
			        $PAGE->menu[] = array( 'name' => 'folio', 
			            'html' => '<li><a href="'.$CFG->wwwroot . $currentusername . '/page/" class="selected">Your Pages</a></li>');
			
			    } else {
			        // Show main menu unselected
			        $PAGE->menu[] = array( 'name' => 'folio', 
			            'html' => '<li><a href="'.$CFG->wwwroot . $currentusername . '/page/">Your Pages</a></li>');
			    
			    }
			} else {
				// Some pages don't have context defined.
				// Show link to folio.
				$PAGE->menu[] = array( 'name' => 'folio', 
					'html' => '<li><a href="'.$CFG->wwwroot . $currentusername . '/page/">Your Pages</a></li>');
				
			}
	    }
	
		if ( defined('context') ) {
			if (substr(context, 0, 10) == 'folio_page') {
	            // Looking at a page.
	            // Look to see if we should include 'Recent Changes' as a menu option. 
	            if ($FOLIO_CFG->wiki_menu_recentchanges == 'Y') {
	                $PAGE->menu_sub[] = array( 'name' => 'folio:recentchanges',
	                    'html' => a_hrefg( "{$CFG->wwwroot}$username/subscribe/html/page+page_comment/",
	                    "Recent Changes"));  
	            }
	            $PAGE->menu_sub[] = array( 'name' => 'folio:edit',
	                'html' => a_hrefg( "{$CFG->wwwroot}$username/page/" .
	                folio_page_encodetitle($page_title) . "/edit",
	                "Edit Page"));  
	            $PAGE->menu_sub[] = array( 'name' => 'folio:history',
	                'html' => a_hrefg( "{$CFG->wwwroot}$username/page/" .
	                folio_page_encodetitle($page_title) . "/history",
	                "History"));  
	            $PAGE->menu_sub[] = array( 'name' => 'folio:delete',
	                'html' => a_hrefg( "{$CFG->wwwroot}$username/page/" .
	                folio_page_encodetitle($page_title) . "/delete",
	                "Delete"));
	            $PAGE->menu_sub[] = array( 'name' => 'folio:print',
	                'html' => a_hrefg( "{$CFG->wwwroot}$username/page/" .
	                folio_page_encodetitle($page_title) . "/print\" target=\"_new\"",
	                "Print"));  
	                
	            // Setup Metatags for RSS Discovery
	            $metatags .= "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS\" href=\"".url."$username/subscribe/rss/page+page_comment/\" />\n";
	
	        }
			
		} 
	
		// -------------------------------------------------------------
		//     SETUP SIDE MENU
		//
		//	Modify side menu setup.  Remove the file & blog side menus in favor of a newly rebuilt & more compact
		//	version used in this mod. 	
		
		// Remove the weblog & file pane. (for everyone but the current user, as it isn't there)
		if ($page_owner != $USER->ident) {
			$function['display:sidebar'] = folio_delete_element( 
				$function['display:sidebar'], 
				$CFG->dirroot . "units/weblogs/weblogs_user_info_menu.php" 
				);
		
			$function['display:sidebar'] = folio_delete_element( 
				$function['display:sidebar'], 
				$CFG->dirroot . "units/files/files_user_info_menu.php" 
				);
		}
	
		// Insert 
		if ( defined('context') && $page_owner != -1 ) {
			switch (context) {
				case 'folio_page_view':
					// Viewing a page
					array_splice( $function['display:sidebar'], 1, 0, 
						array( 	$CFG->dirroot . "mod/folio/page_info_menu.php" ) );
					if ($page_owner != $USER->ident) { //no need for page owner, menu already at top
						array_splice( $function['display:sidebar'], 2, 0, 
							array( 	$CFG->dirroot . "mod/folio/users_info_menu.php" ) );
					}
					break;
				case 'folio_page_edit':
					// Editing a page.  Kill all sidebar entries. Hmm - don't get this one, am adding back. JK
//					$function['display:sidebar'] = 
//						array( $CFG->dirroot . "mod/folio/page_edit_menu.php" );
					//adding back sidebar - JK
					array_splice( $function['display:sidebar'], 1, 0, 
						array( 	$CFG->dirroot . "mod/folio/page_info_menu.php" ) );
					if ($page_owner != $USER->ident) { //no need for page owner, menu already at top
						array_splice( $function['display:sidebar'], 2, 0, 
							array( 	$CFG->dirroot . "mod/folio/users_info_menu.php" ) );
					}
					break;
				default:
					// Viewing normal areas of the website -- aka, not looking at pages.
					// Display link to the user's content.
					if ($page_owner != $USER->ident)
					array_splice( $function['display:sidebar'], 2, 0, 
						array( $CFG->dirroot . "mod/folio/users_info_menu.php" ) );
					break;
			}
		} else {
			//removed this as it doesn't seem to do anything but double-up user count block when not logged in. There's always a context except at login
			// Viewing normal areas of the website -- aka, not looking at pages.
			// Display link to the user's content.
//			if ($page_owner != $USER->ident)
//			array_splice( $function['display:sidebar'], 2, 0, 
//				array( $CFG->dirroot . "mod/folio/users_info_menu.php" ) );
//		
		}
		
		// -------------------------------------------
		// SETUP ALTERNATE RSS
		//
		$function['weblogs:rss:publish'][] = path . "units/weblogs/function_rss_publish.php";
	/*	
		$action = optional_param('action');
		switch ($action) {
			// Create a new weblog post
			case "weblogs:post:add":
				var_dump( $s
	*/
	}
}

/**
* Generic function used to remove an element from an array based off of the element's value (not key)
**/
function folio_delete_element( $array, $target ) {
	$i = 0;
	
	// Remove the weblog & file pane.
	foreach ($array as $value) {
		
		if ( $value == $target ) {
			array_splice ( $array, $i, 1 );
			return $array;
		}
		
		$i++;
	}
	// Not found
	return $array;
	
}


/** 
* Used to generate a 'where' clause for a folio page retrieval operation. Used by tree.php.
* Note that by using this where clause, and running an innner join operation, you are opening the possiblity
* 	of retrieving duplicate records.  To compensate, only retrieve the page values & slap a distinct on top
*	of the query.  It's not the most elegant way of handling dups, but should avoid triggering any bugs
*	from ADODB's keying of returned values by ID.
*
* @uses isloggedin 
* @uses $SESSION['userid']
* @param mixed $tableprefix The alias for the permission table being used.
* @param mixed $tableprefix The alias for the page table being used.
* @param string $level The level of permission that we're looking for...  Values (read, write, owner).
* @param int $profile_id The owner of the pages we're looking at.
* @return A where condition usable for getting content via joining folio_page and folio_page_security
*/
function folio_page_security_where( $tp_permission, $tp_page, $level, $profile_id ) {
	// TODO: Validate input.
	//$user_ident = required_param( $user_ident, int);
    global $CFG;
	$userid = array($_SESSION['userid']);
    
    // Build userid list, giving us a list of memberships.
    if ( isloggedin() ) {

        // Grab community IDs for friends.
        $groupid = recordset_to_array(
    		get_recordset_sql( "SELECT * FROM " . $CFG->prefix . "friends f " .
                " INNER JOIN " . $CFG->prefix . "users u on f.friend = u.ident " .
    			"WHERE f.owner = " . $_SESSION['userid'] . " AND u.user_type='community' ") );
                
        if ( !!$groupid ) {
            foreach ( $groupid as $id ) {
                $userid[] = $id->friend;
            }
        }
        
        // Grab community IDs for owned communities
        $groupid = recordset_to_array(
    		get_recordset_sql( "SELECT * FROM " . $CFG->prefix . "users ".
    			"WHERE user_type = 'community' AND owner = " . $_SESSION['userid'] ) );
        
        if ( !!$groupid ) {
            foreach ( $groupid as $id ) {
                $userid[] = $id->ident;
            }
        }
    }
    
    // Convert array userid list to a sql-able where clause.
	$userid = '(' . implode(', ', $userid ) . ')'; 
   
	// Validate
	if (!($level == 'read' | $level == 'write' | $level == 'owner' )) {
		error(' You can only pass the values read/write/owner to folio_page_security_where in mod\folio\lib.php ');
	}
	
	// Note: Use all upper-case for constants in sql.
	if (!isloggedin() && $level=='read') {
	
		return " ( $tp_permission.accesslevel = 'PUBLIC' " .
			" OR $tp_permission.accesslevel = 'MODERATED' " .
			" OR $tp_permission.accesslevel = 'VIEW_ONLY') ";
			
	} elseif (!isloggedin() && ($level=='write' || $level='owner') ) {
	
		return " ( $tp_permission.accesslevel = 'PUBLIC' ) "; 
		
	} elseif ($level == 'read') {
	
		return " ( $tp_permission.accesslevel = 'PUBLIC' " .
			" OR $tp_permission.accesslevel = 'MODERATED' " .
			" OR $tp_permission.accesslevel = 'VIEW_ONLY' " .
			" OR $tp_permission.accesslevel = 'LOGGED_IN' " .
			" OR ( $tp_permission.accesslevel = 'PRIVATE' AND $tp_page.user_ident IN $userid ) ) ";

	} elseif ($level == 'write' | $level == 'owner') {
	
		return " ( $tp_permission.accesslevel = 'PUBLIC' " .
			"OR ( $tp_permission.accesslevel = 'MODERATED' AND $tp_page.user_ident IN $userid ) " .
			"OR ( $tp_permission.accesslevel = 'PRIVATE' AND $tp_page.user_ident IN $userid ) ) ";
			
	}

}

/**
* Translate the username title to an user record.
*	If the page isn't found, then return false
*
* @todo check that postgress where col = 'abc' is not case sensitive.
*
* @params string $user Username
* @return array MySQL Record.
**/
function folio_finduser( $user ) {
	global $CFG;
	
	// Make sure that incoming paramaters are clean 
	$user = folio_clean( trim( $user ) );

	$user = recordset_to_array(
		get_recordset_sql("SELECT * FROM " . $CFG->prefix . "users " .
		"WHERE LOWER(username) = '$user'")
		);			

	if ( $user ) {
		foreach ($user as $user) {
			// Return 
			return $user;
		}
	} else {
		// Since we didn't find a matching record, return false
		return false;
	}
}

/**
* Translate the page title to an ident.  Ignore security constraints, as this is a fairly safe operation that happens .htaccess rewriting urls.
*	If the page isn't found, then return -1
*
* @todo check that postgress where col = 'abc' is not case sensitive, as well as the LOWER and LIMIT function.
*
* @params string $user Username
* @params string $page The title of the page. Assumes that it has already been decoded by lib.php.
* @return int $page_ident If the page isn't found, then return -1
**/
function folio_page_translatetitle( $user, $page) {
	global $CFG;
	

	$pages = get_records_sql( "SELECT page_ident, p.title FROM " . $CFG->prefix . "folio_page p " .
		"INNER JOIN " . $CFG->prefix . "users u ON p.user_ident = u.ident " .
		"WHERE LOWER(p.title) = ? AND p.newest = 1 AND LOWER(u.username) = ? " . 
		' LIMIT 1 ',
		array( $page, $user ) );
		
	if ( $pages ) {
		foreach ($pages as $page) {
			// Return page ident.
			return $page->page_ident;
		}
	} else {
		// Since we didn't find a matching record, return -1
		return -1;
	}
}

/**
* Remove any non alphanumeric characters in a string
* Legal Characters are a-z, A-Z, 0-9, -, _, and () and spaces
*
* @param string $input
* @return string Clean input
**/
function folio_clean( $input ) {

	$input = trim( $input );
	
	// Test for an easy clean / validation.
	if ( ctype_alnum( str_replace( ' ', '', str_replace( ')', '', str_replace( '(', '', str_replace( '-', '', str_replace( '_', '', $input)) ) ) ) ) ){
		// Validated.  Return
		return $input;
	} else {
		// Step thru & clean each character.
		
		$retval = '';
		
		for ( $i = 0; $i < strlen($input); $i++ ) {
			$v = substr( $input, $i, 1 );
			
			if ( ctype_alnum( $v ) | $v == '-' | $v == '_' | $v == '(' | $v ==')' | $v == ' ') {
				$retval .= $v;
			}
		}
		
		return $retval;
	}
}
/**
* Same as folio_clean, except suitable for array_walk
**/
function folio_clean_array( &$result, $key ) {
	$result = folio_clean( $result);
}


/**
* Retrieve a page.  Ignores security restrictions, as these should be checked with the folio_page_permission function.
*
* @uses $_CFG
* @param int $page_ident
* @param int $created OPTIONAL, pass to pull up an older version of the page.
* @return array MYSQL record.
**/
function folio_page_select( $page_ident, $created = -1 ) {
	global $CFG;
	
    if ($created != -1) {
        // Get old page
		$pages = recordset_to_array(
			get_recordset_sql('SELECT * FROM ' . $CFG->prefix . 'folio_page ' .
				"WHERE page_ident = $page_ident AND created = $created LIMIT 1")
			);
			
    } else {
        // Get current page.
		$pages = recordset_to_array(
			get_recordset_sql('SELECT * FROM ' . $CFG->prefix . 'folio_page ' .
				"WHERE page_ident = $page_ident AND newest = 1 LIMIT 1")
			);
		
	}
	
	return $pages[$page_ident];
}
/**
* Retrieve a page's security records.
*
* @param int $page_ident
* @return array of MYSQL record in array form..
**/
function folio_page_security_select( $page_ident ) {
	global $CFG;
	
	// Create a fake first column to satisfy ADODB's requirement of a unique first field.
	$permissions = recordset_to_array(
		get_recordset_sql( "SELECT CONCAT(user_ident, '.', security_ident) jointkey, " .
			'user_ident, security_ident, accesslevel FROM ' . $CFG->prefix .
			'folio_page_security WHERE ' .
			"security_ident = $page_ident ORDER BY user_ident" ) );

	return $permissions;
}

/**
* Used to see the current user has permission to something.
*   May pull up community records to see if they have membership rights thru that.
*
* @uses $_SESSION[userid]
* @param array $page The page record being checked.  Should be from folio_page_select.  False if we're creating a new page.
* @param array $permissions An array of mysql records of permissions for the current page
* @param string $access The type of permission that we're asking about.  Valid options = read/write
* @param int OPTIONAL $opt_newpageowner An optional page location variable used for signaling where new pages will be located.  UserID of page's location
* @return bit Whether or not the given user has access.
**/
function folio_page_permission( $page, $permissions, $access, $opt_newpageowner = '') {
	global $CFG;
	global $_SESSION;
	global $USER;
		
    // Test for non-logged in users
    if ( !isloggedin() ) {
    	if (!empty($permissions)) {
	        // See if there is a public permission level for write (or moderated for reader)
	        if ( !$page & $access == 'write' ) {
	            // Don't allow non-logged in users to create a new page.
	            return false;
	        } elseif ( $access == 'read' ) {
	            foreach ($permissions as $permission) {
	                if ( $permission->accesslevel == 'PUBLIC' |
	    				$permission->accesslevel == 'MODERATED' |
	    				$permission->accesslevel == 'VIEW_ONLY' ) {
	    				return true;
	                }
	            }
	        } elseif ( $access == 'write' ) {
	    		foreach ($permissions as $permission) {
	    			if ( $permission->accesslevel == 'PUBLIC' ) {
	    				return true;
	    			}
	    		}
	        } elseif ( $access == 'delete' ) {
	                return false;
	        }
	        
	        // Not found anything.  Return false - non-logged in user doesn't have access rights.
	        return false;
    	} else {
    		return false;
    	}
    }
    
    // Continue, knowing that the user is logged in and we have a userid
    
    
	// I had problems directly accessing $_SESSION[userid] and found that
	//		assigning it to a variable solved my boolean evaluation problems
	//		with it.
	$userid = array( $_SESSION['userid'] );

    // Grab community IDs for friends.
    $groupid = recordset_to_array(
		get_recordset_sql( "SELECT * FROM " . $CFG->prefix . "friends f " .
            " INNER JOIN " . $CFG->prefix . "users u on f.friend = u.ident " .
			"WHERE f.owner = " . $_SESSION['userid'] . " AND u.user_type='community' ") );
            
    if ( !!$groupid ) {
        foreach ( $groupid as $id ) {
            $userid[] = $id->friend;
        }
    }
    
    // Grab community IDs for owned communities
    $groupid = recordset_to_array(
		get_recordset_sql( "SELECT * FROM " . $CFG->prefix . "users ".
			"WHERE user_type = 'community' AND owner = " . $_SESSION['userid'] ) );
    
    if ( !!$groupid ) {
        foreach ( $groupid as $id ) {
            $userid[] = $id->ident;
        }
    }

    // Look to see if the user has permission
    if ( !$page ) {
        // New page.  See if it in located in a place where the user has permission to access it.
        return ( in_array( $opt_newpageowner, $userid ) );
    } elseif ( $access == 'read' ) {
        // Page exists, see if we can read it.
		foreach ($permissions as $permission) {
			if ( $permission->accesslevel == 'PUBLIC' |
				$permission->accesslevel == 'MODERATED' |
				$permission->accesslevel == 'VIEW_ONLY' |
				$permission->accesslevel == 'LOGGED_IN' |
				( $permission->accesslevel == 'PRIVATE' && 
				in_array( $page->user_ident, $userid)
				)
				) {
				return true;
			}
		}
	} elseif ( $access == 'write' ) {
        // Page exists, see if we can edit it.
		foreach ($permissions as $permission) {
			if ( $permission->accesslevel == 'PUBLIC' |
				( $permission->accesslevel == 'MODERATED' && 
				in_array( $page->user_ident, $userid)) |
//				$page->user_ident == $_SESSION['userid'] |   - Removed by jklein to enable community edits
				(in_array( $page->user_ident, $userid) && ($USER->owner == -1)) //kill owned user edits, for now
				) {
				return true;
			}
		}
	
	} elseif ( $access == 'delete' ) {
        // See if the user is a member / owner.
        return ( in_array( $page->user_ident, $userid ) && ($USER->owner == -1) ); //kill owned user deletes, for now

    } else {
		error( 'Invalid ' . $access . ' passed to folio_page_permission');
	}
	
    // Didn't catch anywhere, say no access.
	return false;
	
}



/**
* Translates [[Page Title]] to <a href=$url$username/page/$pagetitle/>>$pagetitle</a>.
* 	Note that we don't have a decode, as this should only be called by the view procedure.
*	Encoded urls should not be stored in the database.
*
* @todo Implement some simple checks on the [[ ]] linking.  Stuff like ]]] before [[..., title too long.
* @param string $username The username of the owner of the page.
* @param string $body The body that we're translating.
* @return string The revised body.
**/
function folio_page_makelinks( $username, $body ) {
	$i = true;
	$url = url;
	$body = run("weblogs:text:process", $body);
	while ( $i ) {
		$i = stripos( $body, '[[' );
		$end = stripos( $body, ']]' );
		
		// Test find.
		if ( $i === false | $end === false ) {
			// Reached the end of the line - no more links.
			return $body;
		} else {
			
			// Translate title.
			$link = substr( $body, $i+2, $end -$i -2);
			$body = substr( $body, 0, $i ) .
				"<a href=\"{$url}{$username}/page/" .
				folio_page_encodetitle($link) . 
				"\">$link</a>" . 
				substr( $body, $end+2 );
			
			$i = true;
		}
	}
	
}

/**
* Used to encode a page title.  Strips out illegal characters to make it suitable for use in linking.
*
* @param string Original title
* @return string Encoded title
**/
function folio_page_encodetitle( $title ) {
	//return str_replace( ' ', '_', trim($title) );
	return urlencode( $title );
}

/**
* Used to decode a page title.  
*
* @param string Encoded title
* @return string Original title
**/
function folio_page_decodetitle( $title ) {
	//return str_replace( '_', ' ', $title );
	return urldecode( $title );
}



/**
* Retrieve the homepage for the passed user.
*	If one hasn't been created, will return false
*
* @param string $username.  Assume that it is in normal form (it has not been encoded for html addressing).
* @return array of MYSQL record in array form.  FALSE if not found.
**/
function folio_homepage( $username ) {
	global $CFG;

	$pages = recordset_to_array(
			get_recordset_sql('SELECT p.*, u.username FROM ' . $CFG->prefix . 'folio_page p inner join ' .
				$CFG->prefix . 'users u on p.user_ident = u.ident ' .
				"WHERE p.page_ident = p.parentpage_ident AND u.username = '$username' AND newest = 1 LIMIT 1")
			);

	if ( !$pages ) {
		return false;
	} else {
		foreach ($pages as $page) {
			return $page;
		}
	}
}

/**
* Test to see if the current page is a homepage.
*
* @param $page 
* @return bool TRUE if homepage,  FALSE if not found.
**/
function folio_page_is_homepage( $page ) {

	if ( $page->page_ident == $page->parentpage_ident ) {
		return true;
	} else {
		return false;
	}
}

/**
* Called to create the standard templated homepage for a user. 
*	Will simply return the existing record if it already exists.
*
* @param string $username The user.
* @return int The id for the new record.
**/
function folio_homepage_initialize( $username ) {
	global $CFG;
    global $FOLIO_CFG;
	$url = url;
	
	// Transate the username to an ID
	$user = recordset_to_array(
			get_recordset_sql('SELECT username, ident FROM ' . $CFG->prefix . 'users ' .
				"WHERE username = '$username' LIMIT 1")
			);
	$user = $user[$username];
			
	if ( !$user ) {
		error("Unable to find $username user in folio_homepage_initialize");
	} else {
		$user_ident = $user->ident;
	}
	
	// Check to make sure that the record hasn't already been created in the past.
	$page = recordset_to_array(
			get_recordset_sql('SELECT * FROM ' . $CFG->prefix . 'folio_page ' .
				"WHERE page_ident = parentpage_ident AND user_ident = $user_ident LIMIT 1")
			);			
	
	if ( $page ) {
        $page = array_pop( $page );
		return $page->page_ident;
	} else {
	
    	// Generate ID
    	$page_ident = rand(1,999999999);

    	// Create security record
    	$security = new StdClass;
    	$security->security_ident = $page_ident;
    	$security->user_ident = $user_ident;
    	$security->accesslevel = $FOLIO_CFG->page_defaultpermission;
    	// Insert
    	insert_record('folio_page_security',$security);

    	// Create page record.
    	$page = new StdClass;
    	$page->title = 'Home Page';
    	$page->body = "You can use this part of Elgg as a wiki, to collaboratively work on projects, " . 
    			"or to create an online portfolio.\n\nYou can create links to new pages by simply " .
    			"enclosing the page title using double bracket characters ([ and ]).  Here's an example " .
    			"of a link to this [[Home Page]].";
    	$page->page_ident = $page_ident;
    	$page->security_ident = $page_ident;
    	$page->parentpage_ident = $page_ident;
    	$page->newest = 1;
    	$page->created = time();
    	$page->user_ident = $user_ident;
    	// Insert new record into db.
    	$insert_id = insert_record('folio_page',$page);
    }
	return $page_ident;

}

/**
* Check for a duplicate (or problemtatic) page name
*
* @param int $user_ident The user to whom the page belongs
* @param string $title The title of the page
* @param int $page_ident The id value of the page, -1 if it is a new page
* @param OUTPUT boolean True if there is a dup, false if not.
**/
function folio_duplicate_pagename( $user_ident, $title, $page_ident ) {
    // Clean title just to make sure.
    global $CFG;
    
    $title = folio_clean( $title );

    // Test to see another page exists with the same title.
    $page = recordset_to_array(
        get_recordset_sql('SELECT * FROM ' . $CFG->prefix . 'folio_page ' .
            "WHERE title = '$title' and newest = 1 and user_ident = $user_ident and page_ident <> $page_ident")
        );
    if ( !!$page ) {
        return true;
    } else {
        return false;     
    }
    

}

/**
* Retrieve the username for the passed userid
* @param int $user_ident
**/
function folio_getUsername( $user_ident ) {
    global $name_cache;

    if (!defined('profileinit')) {
        run("profile:init");
    }
   
    if (!isset($username_cache[$user_ident]) 
		|| (time() - $username_cache[$user_ident]->created > 60)) {

        $username_cache[$user_ident]->created = time();
        $username_cache[$user_ident]->data = get_field('users','username','ident',$user_ident);
    }

    return $username_cache[$user_ident]->data;
}

/**
* Publish something to an RSS feed
* 
* @param int owner_ident (int for the object owner)
* @param string owner_username (url-able string name for the object owner)
* @param int user_ident (int for the person doing the action)
* @param string user_username (url-able string name for the user who performed the action)
* @param string user_name (the pretty name of the user who performed the action)
* @param string Type (url-able string, etc: weblog, file, wikipage)
* @param int Type_Ident (string: can be used for update/delete.  Should be unique
	with respect to the type, aka File2, wiki2939, blog234.  I like this
	way of keeping track of unique items (as opposed to the link or some
	other attribute) because all of the add-ins and native data types have
	an ident value in the database to store their values.  It would
	probably make sense to allow this field to be null, in which case, the
	rss item could not be updated/deleted in the future.).
* @param array Tags (an array of string, eg. array("wiki", "elgg", "notes") ).
* @param string Title (string title for the RSS item).
* @param string Body (full string for the message to be syndicated.)
* @param string Link (the http address to view/comment on/edit/whatever the item).
* @param string access (the permission level for the rss item, def=public).
**/
function rss_additem( $owner_ident, $owner_username, $user_ident, $user_name, $user_username, $type, $type_ident, $tags, $title, $body, $link, $access='PUBLIC' ) {

	$rss = new StdClass;
	$rss->owner_ident = intval($owner_ident);
	$rss->owner_username = $owner_username;
	$rss->user_ident = intval($user_ident);
	$rss->user_name = $user_name;
	$rss->user_username = $user_username;
	$rss->type = $type;
	$rss->type_ident = $type_ident;
	$rss->title = $title;
	$rss->body = $body;
	$rss->link = $link;
	$rss->created = time();
    $rss->access = $access;
	
	// Insert new record into db.
	insert_record('folio_rss',$rss);
}

/**
* Retrieve records from an rss feed.
* 
* @param string $where (There where clause for the query)
* @param int $from (Where to start showing results from, def = 0
* @param int $perpage How many results to show after the start, def = 10
* @param array OUTPUT (The returned records in an array of arrays corresponding to the mysql records.)
**/
function rss_getitems( $where, $from=0, $perpage=10 ) {
	global $CFG;

   $results = recordset_to_array(get_recordset_sql( "SELECT CONCAT(type_ident, '.', type, '.', created) masterkey, " .
		$CFG->prefix . "folio_rss.* FROM " . $CFG->prefix . "folio_rss" .
		" WHERE $where" .
		" ORDER BY created DESC " .
		" LIMIT $from, $perpage " ));
	return $results;
}

/**
* Retrieve the number of records available in an RSS feed.
*
* @param string $where ( the where clause for the query)
* @param array OUTPUT an Integer value.
**/
function rss_getcount( $where ) {
	global $CFG;
	// Clean incoming variables.

   $results = recordset_to_array(get_recordset_sql( "SELECT '1' as ident, count(*) as records " .
		" FROM " . $CFG->prefix . "folio_rss" .
		" WHERE $where " ));

	return intval($results[1]->records);

}

/**
* Retrieve all of the allowable permission types for the passed userid
* @param int $userid -1 is a valid option, just sets to public permissions.
**/
function rss_permissionlist( $userid ) {
    global $CFG;
        
    if ( $userid == -1 ) {
        return array('PUBLIC', 'MODERATED', 'VIEW_ONLY');
    }
    
    // Default permissions already availabled to logged-in users.
    $list = array('PUBLIC', 'LOGGED_IN', 'MODERATED', 'VIEW_ONLY');
    
    // Find values for joined communities.
    $communities = recordset_to_array(get_recordset_sql( 
        "SELECT distinct u.ident as i, u.ident FROM " . $CFG->prefix . "friends e inner join " . 
            $CFG->prefix . "users u on e.friend = u.ident where e.owner=$userid and user_type = 'community'"));
    
    if ( $communities) {
        foreach ( $communities as $community) {
            $list[] = 'community' . $community->ident;
        }
    }
    
    // Find values for owned communities
    $communities = recordset_to_array(get_recordset_sql( 
        "SELECT DISTINCT ident as username, ident FROM " . $CFG->prefix . "users where owner = $userid"));
    
    if ( $communities ) {
        foreach ( $communities as $community ) {
            $list[] = 'community' . $community->username;
        }
    }
    
    // Find values for joined groups.
    $groups = recordset_to_array(get_recordset_sql( 
        "SELECT distinct g.ident as i, g.ident FROM " . $CFG->prefix . "group_membership m inner join " . 
            $CFG->prefix . "groups g on m.group_id = g.ident where m.user_id=$userid "));
    
    if ( $groups) {
        foreach ( $groups as $group) {
            $list[] = 'group' . $group->ident;
        }
    }
    
    // Find values for owned groups
    $groups = recordset_to_array(get_recordset_sql( 
        "SELECT DISTINCT ident as i, ident FROM " . $CFG->prefix . "groups where owner = $userid"));
    
    if ( $groups ) {
        foreach ( $groups as $group ) {
            $list[] = 'group' . $group->ident;
        }
    }
    
    return $list;
}  

/**
* Return the ident for the user reading this page
*   If no validation, then return -1
**/    
function folio_decodehash( $readerhash ) {

    // To view subscribe records, validate hash.  If fail, return false.
    if ( $readerhash == '' ) {
        return -1;
    } else {
        // Calculate MD5 hash
        $i = strpos( $readerhash, ':' );
        
        // Separate variables
        $reader_name = substr( $readerhash, 0, $i );
        $reader_pwd = substr( $readerhash, $i+1, strlen($readerhash) - $i -1);
        // Grab ID
        $rec = get_record('users','username',$reader_name);
        
        if ( $rec->password == $reader_pwd ) {
            return $rec->ident;
        } else {
            return -1;
        }
        
    }
}

/**
* Return the hash values for a userident
**/    
function folio_createhash( $user_ident) {
    $rec = get_record('users','ident',$user_ident);
    return $rec->username . ':' . $rec->password;
}
    

?>