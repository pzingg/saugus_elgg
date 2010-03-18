<?php
/**
* This function will display a security control for a page.
* Warning: Do not change values for field names w/o also updating the save procedure.
*
* @package folio
* @param $page Required, the page being shown.
*/

/*
* Build the html code for a security box.
*
* @returns HTML control information.  
*/
function folio_control_page_edit_security( $page ) {
    // Find the security information for the page.
	global $CFG;
    global $FOLIO_CFG;
    $url = url;
	$accesslevel = '';
	$page_ident = intval($page->page_ident);
	$parentpage_ident = intval($page->parentpage_ident);
	$security_ident = intval($page->security_ident);
	
	// Find out what security-level we're set at.
    $accesslevels = recordset_to_array(
		get_recordset_sql('SELECT security_ident, accesslevel FROM ' . $CFG->prefix . 'folio_page_security '  .
			"WHERE security_ident = $security_ident LIMIT 1")
		);

    if (!$accesslevels[$security_ident]) {
        // No results returned.  Could be because we don't have permission to access the parent pages, or that the parent may have been removed/deleted.
		// Set the default access level.
		$accesslevel = $FOLIO_CFG->page_defaultpermission;
    } else {
        // Grab the record entry.
        $accesslevel= stripslashes( $accesslevels[$security_ident]->accesslevel );
	}	
	
	// Figure out which option is selected & set value.
	$oPublic = ''; $oModerated = ''; $oPrivate = '';
	if ( $accesslevel == 'PUBLIC' ) {
		$oPublic = ' SELECTED=true ';
	} elseif ( $accesslevel == 'MODERATED' ) {
		$oModerated = ' SELECTED=true ';
	} elseif ( $accesslevel == 'PRIVATE' ) {
		$oPrivate = ' SELECTED=true ';
	} elseif ( $accesslevel == 'LOGGED_IN' ) {
		$oLoggedIn = ' SELECTED=true ';
	} elseif ( $accesslevel == 'VIEW_ONLY' ) {
		$oViewOnly = ' SELECTED=true ';
	}
	
	// If the user isn't logged in, then they are not allowed to change the permission level for the page.
	if ( !isloggedin() ) {
		// finish building the control, but mark it as disabled..
		$run_result = <<< END
			<SELECT NAME="folio_control_page_edit_security_custom" DISABLED>
				<OPTION VALUE="PUBLIC" {$oPublic}>Public (everyone has full access)
				<OPTION VALUE="MODERATED" {$oModerated}>Moderated (other people can read, friends can edit)
				<OPTION VALUE="VIEW_ONLY" {$oViewOnly}>View Only (other people can read only)
				<OPTION VALUE="LOGGED_IN" {$oLoggedIn}>Logged In (only logged in users can read)
				<OPTION VALUE="PRIVATE" {$oPrivate}>Private (other people can not read or edit)
			</SELECT><b> You must first log in to change permissions.</b>
		<br/>
END;
		
	} else {
		// finish building the control.
		$run_result = <<< END
			<SELECT NAME="folio_control_page_edit_security_custom">
				<OPTION VALUE="PUBLIC" {$oPublic}>Public (everyone has full access)
				<OPTION VALUE="MODERATED" {$oModerated}>Moderated (other people can read, friends can edit)
				<OPTION VALUE="VIEW_ONLY" {$oViewOnly}>View Only (other people can read only)
				<OPTION VALUE="LOGGED_IN" {$oLoggedIn}>Logged In (only logged in users can read)
				<OPTION VALUE="PRIVATE" {$oPrivate}>Private (other people can not read or edit)
			</SELECT>
		<br/>
END;
	}
	
	
	return templates_draw(array(
                                'context' => 'databoxvertical',
                                'name' => 'Security',
                                'contents' => $run_result
                            )
                            );
}


/**
* DEPRECIATED -- NOT USED. Just in case this is needed again, I left in it place.
* 
* Recursively get parent security information. Called by folio_control_page_edit_security
*
* @todo It doesn't make sense to have a page inheriting a parent's permissions if the parent is also inheriting.  We should intelligently set this page to the same root page.
*
* @param int $page_ident The page to retrieve information on.  Recurse on this page's parent value.
* @param int $security_ident The original calling page's security ID.  Stays the same throughout recursion.
* @param string $url Same as url, just passed to increase speed.
* @param string &$accesslevel OUTPUT Param, used to return the accesslevel if a &page_ident == $security_ident.  
* @returns HTML control information.  If a page is found that matches the passed security ident, then select that control.
*/
/*
function folio_control_page_edit_security_recursive_find($page_ident, $security_ident, $url, &$accesslevel) {
    // Recursively find the parent information.
	// If the recursively-found information matches the security id, then select it.
    global $CFG;
	
    // Test for exit condition.
    if ($page_ident < 0) {
        return '';
    }
    
    //   Find the root page information.
    $pages = recordset_to_array(
		get_recordset_sql("SELECT w.*, p.accesslevel FROM " . $CFG->prefix . "folio_page w " .
			"INNER JOIN " . $CFG->prefix . "folio_page_security p ON w.security_ident = p.security_ident " .
			"WHERE w.page_ident = $page_ident AND w.newest = 1 AND " . 
			folio_page_security_where( 'p', 'read' ) . ' LIMIT 1')
		);


    if (!$pages[$page_ident]) {
        // No results returned.  Could be because we don't have permission to access the parent pages, or that the parent may have been removed/deleted.
		return '';        
    } else {
        // Grab the record entry.
        $page = $pages[$page_ident];
        $parentpage_ident = $page->parentpage_ident;
        $title = stripslashes($page->title);
        // Recurse.
		if ( $page_ident == $security_ident ) {
			// Set the security level.
			$accesslevel = stripslashes($page->accesslevel);
			// return selected
			return "<OPTION VALUE=\"{$page_ident}\" SELECTED=True>" . stripslashes($page->title) .
				folio_control_page_edit_security_recursive_find(
					$parentpage_ident, $security_ident, $url, $accesslevel);
		} else {
			return "<OPTION VALUE=\"{$page_ident}\">" . stripslashes($page->title) .
				folio_control_page_edit_security_recursive_find(
					$parentpage_ident, $security_ident, $url, $accesslevel);
		}
    }        
}
*/
?>