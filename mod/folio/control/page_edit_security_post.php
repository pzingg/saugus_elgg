<?php
/**
* Function: 
*	This is the postback page for the page_edit_security.php control.
*	The html control output from that page should be passed to this file for processing.
*	Output from this page is used by page_edit_post.php.
* Note:
* 	Requires the user to be logged in.  Users not logged in cannot set permissions.
*	It will not fail, but will instead use the previously-set security settings.
* Note:
*	As it stands now, the security system is not fully implemented.  The userid field in security
*	table is ignored and set to -1
*
* Dependencies:
*	The page_edit_post.php is called after this one, and depends upon the $security_ident variable being properly set.
*
*
* @todo Upon converting to AJAX poststyle (if done), look into loading a lighter version of includes.php.
*
* @package folio
* @uses includes.php
* @uses $CFG
* @param int $page_ident  The identity key for the page.
* @param string $folio_control_page_edit_security_type The type of security ( parent or custom)
* @param int $folio_control_page_edit_security_parent OPTIONAL The parent security id for the page.
* @param string $folio_control_page_edit_security_custom The custom level of security for the page.
**/

	// Note, this is ../ insead of ../../../ because we're called by files in /_folio, and not from the folder that this 
	//	page is actually residing inside of.
	require_once('../includes.php');
	
	$security_ident = -1;
	
	// Load variables
	$page_ident = required_param('page_ident', PARAM_INT);
	
	$security_type = optional_param('folio_control_page_edit_security_type', '-1');
	$security_parent = optional_param('folio_control_page_edit_security_parent', -1,PARAM_INT);
	$security_custom = optional_param('folio_control_page_edit_security_custom', '-1');
	
	// Test to see if we're logged in. 
	if ( !isloggedin() ) {
		// Since the user isn't logged in, don't allow them to change the security settings for the page.

		//	Get the previous copy's information.
		$pages = recordset_to_array(get_recordset_select('folio_page', 
			"page_ident = $page_ident and newest= 1", 
			null, 'title', '*'));
		
		if (!$pages[$page_ident]) {			
			// Not found.
			error(' Unable to retrieve old record in folio_actions on line 46 for page ' . 
				$page_ident .  '.  You may have tried to create a new page without logging in.' .
				'  Please log in and try again.');
		} else {
			$security_ident = $pages[$page_ident]->security_ident;
		}
	
	} else {
		// Since we're logged in, allow user to set permissions.
	
		// Check to see if we're inheriting.
		if ( $security_type == 'Parent') {
			// Inheriting
			$security_ident = $security_parent;
		} else {
			// Set to a custom security level.  We'll need to insert/update the security record.
			$security_ident = $page_ident;
			
			// Validate accesslevel
			if ($security_custom <> 'PUBLIC' & $security_custom <> 'MODERATED' & $security_custom <> 'VIEW_ONLY' & $security_custom <> 'LOGGED_IN' & $security_custom <> 'PRIVATE') {
				error('Invalid access level passed to page_edit_security_post (' . $security_custom . ')' );
			}

			// Test to see if the record already exists.
			$pages = recordset_to_array( get_recordset_sql("SELECT * FROM " . $CFG->prefix . 	
				'folio_page_security WHERE security_ident = ' . $page_ident .
				' LIMIT 1' ) );
			
			if (!$pages[$page_ident]) {
				// Insert a new security record.

				$security = new StdClass;
				$security->security_ident = $page_ident;
				$security->user_ident = -1;  // NOTE: this is the owner, not the creator.
				$security->accesslevel = $security_custom;
				insert_record('folio_page_security',$security);
			} else {
				// Update security record(s)
				set_field('folio_page_security', 'accesslevel', $security_custom, 
					'security_ident', $page_ident);
			}

		} 
	
	}
 
?>