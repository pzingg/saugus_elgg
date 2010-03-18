----------------------------------------------------------------------------------
                                Elgg Folio Add-in
                                Updated 11/11/2006
----------------------------------------------------------------------------------

/////////////////////////////////////////////////////////////////////////////////
// OVERVIEW
/////////////////////////////////////////////////////////////////////////////////

This add-in provides basic wiki functionality for the Elgg social networking
software.

Please view the changelog to see version compatability.  Because this add-in 
changes some of the default Elgg files, it has to be loaded for the appropriate
version.

This add-in modifies some of the way that Elgg operates.  It rewrite the side-menu
to include pages.  It includes a unique key searchable on Google with the add-in
version (see users_info_menu.php).

Please note that this svn repository contains files that are not used by this add-in.
The only files needed to install this folio add-in are found in mod/folio, _folio, and
the .htaccess file.

/////////////////////////////////////////////////////////////////////////////////
// TEST INSTALLATIONS
/////////////////////////////////////////////////////////////////////////////////

This add-in has been tested on a number of different systems.  It is developed on a
Windows XP box running Apache2Triad.  It is run on Linux RHE system.  

At this point, it does not work on any database other than MySQL.

At this point, it is not internationalized with gettext.

If you experience any problems, please contact Nathan Garrett at nathan.garrett@cgu.edu

/////////////////////////////////////////////////////////////////////////////////
// INSTALLATION or UPGRADING
/////////////////////////////////////////////////////////////////////////////////

To install this software, do the following:

1. Install the v0.651 (or newer) version of Elgg.  Make sure that Elgg is working
	before going any further.
2. If you are upgrading, remove any previous installations of the wiki add-in by 
	deleting all of the files in  _folio and mod/folio.  Remove the extra lines
	in .htaccess marked as belonging to this add-in.
3. Copy the _folio folder to {root folder}_folio, and the the mod/folio folder to
	{root folder}mod/folio.
4. Copy the section marked as belonging to this add-in in the .htaccess file in this distribution
	to your .htaccess file.  I do not recommed replacing your existing .htaccess with
	this one, as new versions of Elgg can introduce modified rules.
5. Log into Elgg as news.
6. Go the {root folder}_folio/setupdb.php.  This file will update the database. You have
	to be logged in as news to be able to run this file.  If you do not complete this
	step, the add-in will not work properly.  Running this file will not remove
	any of your existing data.  It simply updates your database to the most recent version.
	Running this multiple times will not cause any problems.  It should just tell you
	that your database is up to date.
7. There are a number of options in /mod/folio/config.php that can be set.  For example,
        you can disable the javascript downdown in the new side menu. You do not have to 
        move or rename this file for the options to work, just edit it as is.


You should now have a fully-working version of the Elgg Wiki Add-in


/////////////////////////////////////////////////////////////////////////////////
// OTHER MODULES
/////////////////////////////////////////////////////////////////////////////////

CGU SL2 has also released a number of other Elgg add-ins.  

* Subscribe: This adds global RSS functionality to Elgg, including a custom
	log-on page showing recent activity in the system.
* Chatbox: This adds a chat screen global to all of Elgg.  It acts as a thing
	wrapper to YShout.

/////////////////////////////////////////////////////////////////////////////////
// Contributors
/////////////////////////////////////////////////////////////////////////////////

Research Advisors:
	Dr. Lorne Olfman (CGU, Social Learning Lab. Dean, IS&T)
	Dr. Terry Ryan (CGU, Social Learning Lab)
Main Programmer: 
	Nathan Garrett (blog at elgg.net/garrettn)
Custom Templating: 
	Brian Thoms 
Camtasia Walkthrus:
	Mariana Soffer
Financial Support:
	CGU Transdisciplinary Grant
		Sponsor: Dr. Wendy Martin (CGU George and Ronya Kozmetsky Chair)

	
/////////////////////////////////////////////////////////////////////////////////
// Change Log & Schedule
/////////////////////////////////////////////////////////////////////////////////

Anticipated:
	2007.06.?? v1.0 
	2007.01.01 Release the global rss code integrated into the wiki.

Occurred:
	2006.11.11 Released the working v0.4 version.
	2006.10.23 Beta v0.4 Finished getting the subscribe/activity functionality working.
	2006.10.04 v0.31 Added history & delete functionality. Minor bug fixes.
	2006.08.28 v0.3 
	2006.07.26 Add-in installed at claremontconversations.org/tcourse
	2006.06.26 v0.2 (Alpha 2). Compat with Elgg v.6
	2006.06.16 SVN setup at sl2.cgu.edu/svn/elgg.  Public read-only access granted.
	2006.05.?? Awarded CGU T-Course Grant 
	2006.02.26 v0.1 (Alpha 1). Compat with Elgg v.4.
	2006.01.01 Started Work

/////////////////////////////////////////////////////////////////////////////////
// Libraries
/////////////////////////////////////////////////////////////////////////////////    
    
This work was partially built off of a number of pubically available and open-sourced
php libraries.

Yahoo Control Library - Treeview control
Cross-Browser.com - Drop-down window
Yav - Javascript Validation    
YShout - Chatbox (optional, separate download)

/////////////////////////////////////////////////////////////////////////////////
// License
/////////////////////////////////////////////////////////////////////////////////

    This file is part of Elgg Folio Addin.

    Elgg Folio Addin is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Elgg Folio Addin is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Elgg Folio Addin; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA