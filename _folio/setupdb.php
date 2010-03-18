<?php
/**
* This file acts to keep the db scheme updated.  It should be run on the mysql backend db after code has been
* 	updated.  It is safe to run on existing data, as it will look at the current db storage scheme & update it
*	without removing existing data.
* You have to be logged in as news to run this file.  Users trying to access it without having that login will
*	be presented with an error.
*
* @package folio
* @todo Enable functionality with Postgress, currently only works with mysql.
**/

// The current version of the folio db scheme.
$folio_dbversion = 5;

// Setup variables.
define("context" , "setup_folio");
global $CFG,$db;
require("../includes.php");
require_once '../mod/folio/config.php';
    
// See if we're using postgreesss or an old version of mysql.  Cancel, as this only works with MySQL.
if ($CFG->dbtype == 'postgres7') {
	error('This only works with a MySQL database');
	die();
}

if (!$rs = $db->Execute("SELECT version();")) {
	error('Error accessing MySQL database when trying to run SELECT version()');
	die();
}

if (intval($rs->fields[0]) <= 3) {
	error('Error, you are using a version of mysql that is out of date.  You have to use 4.0 or greater.');
	die();
}

// See if a user other than news is trying to run this.
if ($USER->username <> 'news') {
	error('You can only run this function if you logged on as "news"');
	die();
}

// Start upgrading.
if ( folio_getVersion() < $folio_dbversion ) {
	echo ( 'Your DB scheme does not match the most current version.  Beginning upgrade.');
		
	switch ( folio_getVersion() ) {
			case -1:
			   gotoVersion1();
			   // no break, need to continue updating.
			case 1:
			   gotoVersion2();
			   // no break, need to continue updating.
			case 2:
			   gotoVersion3();
			   // no break, need to continue updating.	
			case 3:
			   gotoVersion4();
			   // no break, need to continue updating.	
			case 4:
			   gotoVersion5();
			   // no break, need to continue updating.	
   }
	echo ( '<br/>Update finished<br/>' );
} else {

	echo ( 'Your DB scheme is up to date.  Exiting function.');
}

/**
* Get the current version, or -1 if the table isn't present
* @return Integer
**/
function folio_getVersion() {
	global $CFG;
	
    // Find.
    $versions = recordset_to_array(
		get_recordset_sql("SELECT version as v, version FROM " . $CFG->prefix . "folio_version")
		);
	
    if ( $versions ) {
		$i = -1;
        foreach ($versions as $version) {	
			if ($i < $version->version) {
				$i = $version->version;
			}
		}
		return $version->version;
	} else {
		// Table not found.  Folios probably aren't installed yet.
		return -1;
	}
}

/** 
* Update to version 5, added indexing & a new column for RSS.
**/
function gotoVersion5() {
	global $CFG;	
	$prefix = $CFG->prefix;
	$db = $CFG->dbname;

	execute_sql("
ALTER TABLE {$db}.{$prefix}folio_rss 
    ADD COLUMN access VARCHAR(255) NOT NULL DEFAULT 'PUBLIC' AFTER owner_username;
 ");

    execute_sql("
ALTER TABLE {$db}.{$prefix}folio_rss 
    ADD INDEX Index_user(user_ident),
    ADD INDEX Index_owner(owner_ident);
 ");
	execute_sql("
ALTER TABLE {$db}.{$prefix}folio_page
    ADD INDEX Index_user(user_ident),
    ADD INDEX Index_title(title),
    ADD INDEX Index_newest(newest),
    ADD INDEX Index_parent(parentpage_ident);
");

	execute_sql("
ALTER TABLE {$db}.{$prefix}folio_comment
    ADD INDEX Index_type(item_type),
    ADD INDEX Index_ident(item_type);
 ");

    execute_sql("
ALTER TABLE {$db}.{$prefix}folio_page_security
    ADD INDEX Index_security(security_ident);
 ");

	$version = new StdClass;
	$version->version = 5;
	insert_record("folio_version",$version);	
}

/** 
* Update to version 4, updated comment table to differentiate between owner & poster
**/
function gotoVersion4() {
	global $CFG;	
	$prefix = $CFG->prefix;
	$db = $CFG->dbname;

	execute_sql( 
			"
ALTER TABLE {$db}.{$prefix}folio_comment CHANGE COLUMN `owner` `creator_ident` INTEGER NOT NULL DEFAULT 0,
 CHANGE COLUMN `postedname` `creator_name` VARCHAR(254) NOT NULL DEFAULT '',
 ADD COLUMN `item_owner_ident` INTEGER UNSIGNED NOT NULL DEFAULT 0 AFTER `posted`,
 ADD COLUMN `item_title` VARCHAR(254) NOT NULL DEFAULT '' AFTER `item_ident`,
 ADD COLUMN `item_owner_username` VARCHAR(254) NOT NULL DEFAULT '' AFTER `item_title`,
 ADD COLUMN `item_owner_name` VARCHAR(254) NOT NULL DEFAULT '' AFTER `item_owner_username`,
 ADD COLUMN `creator_username` VARCHAR(254) NOT NULL DEFAULT '' AFTER `item_owner_name`;
 "
	);

	execute_sql( 
			"
ALTER TABLE {$db}.{$prefix}folio_rss ADD COLUMN `owner_username` VARCHAR(255) NOT NULL DEFAULT '' AFTER `created`;
 "
	);
	

	
	$version = new StdClass;
	$version->version = 4;
	insert_record("folio_version",$version);	
}


/** 
* Update to version 3, added rss table
**/
function gotoVersion3() {
	global $CFG;	
	$prefix = $CFG->prefix;
	$db = $CFG->dbname;

	execute_sql( 
			"
CREATE TABLE {$db}.{$prefix}folio_rss (
  `type_ident` int(10) unsigned NOT NULL default '0',
  `type` varchar(45) NOT NULL default '',
  `user_ident` int(10) unsigned NOT NULL default '0',
  `user_username` varchar(45) NOT NULL default '',
  `user_name` varchar(255) NOT NULL default '',
  `owner_ident` int(10) unsigned NOT NULL default '0',
  `title` text NOT NULL,
  `body` text NOT NULL,
  `link` varchar(255) NOT NULL default '',
  `created` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`type_ident`,`type`,`created`))"
	);

	$version = new StdClass;
	$version->version = 3;
	insert_record("folio_version",$version);	
}


/** 
* Update to version 2, added a column to a table.
**/
function gotoVersion2() {
	global $CFG;	
	$prefix = $CFG->prefix;
	$db = $CFG->dbname;

	execute_sql( 
			"ALTER TABLE {$db}.{$prefix}folio_page ADD creator_ident Integer AFTER created;"
			);

	execute_sql( 
			"UPDATE {$db}.{$prefix}folio_page SET creator_ident = user_ident;"
			);

			

	$version = new StdClass;
	$version->version = 2;
	insert_record("folio_version",$version);	
}



/** 
* Update to version 1 -- first db schema
**/
function gotoVersion1() {
	global $CFG;	
	$prefix = $CFG->prefix;
	$db = $CFG->dbname;
			
/* ---------------------------------------------------------------
Page
Currently not using formatpage_ident column.  Built in now to support templating.
--------------------------------------------------------------- */
	execute_sql( 
			"CREATE TABLE  `{$db}`.`{$prefix}folio_page` (
			  `page_ident` int(11) NOT NULL default '0',
			  `user_ident` int(11) NOT NULL default '0',
			  `created` int(11) NOT NULL default '0',
			  `title` varchar(255) NOT NULL default '',
			  `newest` tinyint(1) NOT NULL default '0',
			  `parentpage_ident` int(11) NOT NULL default '-1',
			  `format_ident` int(11) NOT NULL default '0',
			  `body` text NOT NULL,
			  `security_ident` int(11) NOT NULL default '-1',
			  PRIMARY KEY  (`page_ident`,`created`)
			);"
			);

/* ---------------------------------------------------------------
 Page Security

There can be multiple security records for a single page.  They differ
only in that they have multiple user_ids.  They should all have the same
security levels.  This lets us keep track of the different involved users.

Currently 3 levels are implemented, public, moderated, and private.  Public 
is wide open, everyone can read/write.  Moderated is public readable, but
only setable by owners.  Private is readable and setable only by owners.
Owners are defined as people who have a security record with their id.

The interface doesn't currently handle inheriting.  Will re-implement again 
at a later date.
		 --------------------------------------------------------------- */
	execute_sql( 
			"CREATE TABLE  `{$db}`.`{$prefix}folio_page_security` (
			  `security_ident` int(11) NOT NULL default '0',
			  `user_ident` int(11) NOT NULL default '0',
			  `accesslevel` varchar(100) NOT NULL default 'member',
			  PRIMARY KEY  (`user_ident`,`security_ident`,`accesslevel`)
			);"
			);

/* ---------------------------------------------------------------
Tree

This contains nodes for navigating between objects.  

Probably needs some more indexes (along with the rest of the folio
tables).

Not currently being used.  Will implement for the presentation module, and possibly 
the ability to import/link to other people's pages.
--------------------------------------------------------------- */
	execute_sql( 
			"CREATE TABLE  `{$db}`.`{$prefix}folio_tree` (
		  `node_ident` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
		  `alink_type` VARCHAR(45) NOT NULL DEFAULT '',
		  `alink_ident` INTEGER UNSIGNED NOT NULL DEFAULT 0,
		  `blink_type` VARCHAR(45) NOT NULL DEFAULT '',
		  `blink_ident` INTEGER UNSIGNED NOT NULL DEFAULT 0,
		  `blink_caption` VARCHAR(45) NOT NULL DEFAULT '',
		  PRIMARY KEY(`node_ident`),
		  INDEX `a_link`(`alink_ident`),
		  INDEX `b_link`(`blink_ident`)
			);"
			);

/* ---------------------------------------------------------------
Folio Comments.
Comments on different items inside of the add-in.
--------------------------------------------------------------- */

	execute_sql( 
			"CREATE TABLE  `{$db}`.`{$prefix}folio_comment` (
			  `activity_ident` int(11) NOT NULL auto_increment,
			  `item_type` varchar(100) NOT NULL default '',
			  `item_ident` int(11) NOT NULL default '0',
			  `owner` int(11) NOT NULL default '0',
			  `postedname` varchar(100) NOT NULL default '',
			  `body` text NOT NULL,
			  `access` varchar(100) NOT NULL default '',
			  `posted` int(11) NOT NULL default '0',
			  PRIMARY KEY  (`activity_ident`)
			);"
			);

/* ---------------------------------------------------------------
Tracks what version of the db we're working with.
--------------------------------------------------------------- */
			
	execute_sql(
			"CREATE TABLE  `{$db}`.`{$prefix}folio_version` (
			  `version` int(10) unsigned NOT NULL auto_increment,
			  PRIMARY KEY  (`version`)
			);"
	);
			
// Update version to schema 1
	$version = new StdClass;
	$version->version = 1;
	insert_record("folio_version",$version);

}
?>