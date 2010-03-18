<?php
/**
* Edit a wiki page.
*
* @package folio
**/
include_once( 'page_edit_security.php' );
require_once('page_edit_move.php');

/**
* Edit a single wiki page (creating a new copy in the db, and marking the old one as not current).
* 	 or insert a new wiki page if no page_ident value is passed.
* Note: do not change html field names w/o also changing those in action_redirection.
*
* @package folio
* @param string $page The mysql page record.
* @param string $permissions The mysql permissions records.
* @param string $page_title The passed in title used to access the page.  Important
* 	in case we're building a new page.  Assumes that it has already been decoded by the function in lib.php
*	away from the URL form & into the normal presentation form.
* @param string $username The username of the page owner.  Used to create post link
* 	to the finished page.
* @param int $parentpage_ident The page being edited, or the parent page of the new page
* @todo Change way the pk for a new wiki page is created.
* @returns HTML code to edit a folio page.
**/

function viewfolder($folderid, $userid, $level) {

    $prefix = "";
    for ($i = 0; $i < $level; $i++) {
        $prefix .= "&gt;";
    }
    $fileprefix = $prefix . "&gt;";
    
    if ($folderid == -1) {
        $body = <<< END
                <option value="">ROOT</option>
END;
    } else {
        $current_folder = get_record('file_folders','owner',$userid,'ident',$folderid);
        $name = strtoupper(stripslashes($current_folder->name));
        $body = <<< END
                    <option value="">{$prefix} {$name}</option>
END;
    }
    if ($files = get_records_select('files',"owner = ? AND folder = ?",array($userid,$folderid))) {
        foreach($files as $file) {
            $filetitle = stripslashes($file->title);
            $body .= <<< END
                    
                    <option value="{$file->ident}">{$fileprefix} {$filetitle}</option>
END;
        }
    }
    
    if ($folders = get_records_select('file_folders',"owner = ? AND parent = ? ",array($userid,$folderid))) {
        foreach($folders as $folder) {
            $body .= viewfolder($folder->ident, $userid, $level + 1);
        }
    }
    return $body;
}

function folio_page_edit($page, $permissions, $page_title, $username, $parentpage_ident=-1) {
    global $CFG;
    global $profile_id;
    global $language;
    global $page_owner;
	global $metatags;
    
    if (!$page && !isloggedin() ) {
		// We don't allow non-logged in users to create new pages
		error('You can not create a new page without logging in.');
	} elseif (!$page) {
        // Set values for the new page.

        // @TODO: Find a better random method, or at least verify that the page doesn't already exist.
        // Generate a new value for the page key.
		// If we change this, also update the stuff in lib that creates a new record.
        $page_ident = rand(1,999999999);
		// If this page is being created because of a hyperlink, then we
		//	already know what it should be named.  If not, then 
		//	set to something that obviously needs to be changed.
		if ( ! strlen( $page_title ) > 0 ) {
			$title = 'New Page Title';
		} else {
	        $title = $page_title; 
		}
        $body = 'Create your new page here!'; 

		// Translate passed username into a user ident.
		$user_ident = folio_finduser($username);
		if ( !$user_ident ) {
			error('mod\folio\control\page_edit.php was called without a username parameter ' .
				"or with an invalid one ($username)");
		}
		$user_ident = $user_ident->ident;
		
		$page = new StdClass;
		$page->page_ident = $page_ident;
		$page->title = $title;
		$page->body = $body;
		$page->security_ident = $page_ident;
		$page->parentpage_ident = $parentpage_ident;
		$page->user_ident = $user_ident;
		$username = $username;		
		
    } else  {
        // Clean DB Entries, making them suitable for displaying.
        $page_ident = intval($page->page_ident);
        $body = stripslashes($page->body);
        $title = stripslashes($page->title);
        $parentpage_ident = intval($page->parentpage_ident);  
		$security_ident = intval($page->security_ident);
		$user_ident = intval($page->user_ident);
    }
	
	// Include javascript for the tinymce rich editor.

	// Lose the trailing slash
	$url = substr(url, 0, -1);
	
	//get current language
	if (empty($CFG->userlocale)) {
		$lang = substr($CFG->defaultlocale, 0, 2);
	} elseif (is_array($CFG->userlocale)) {
		$lang = substr($CFG->userlocale[0], 0, 2);
	} else {
		$lang = substr($CFG->userlocale, 0, 2);
	}

	$metatags .= <<< END
		<SCRIPT type="text/javascript" src="$url/mod/folio/control/yav1_2_3/js_compact/yav.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="$url/mod/folio/control/yav1_2_3/js_compact/yav-config.js"></SCRIPT>
	    <script language="javascript" type="text/javascript" src="$url/_tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
		
	    <script language="javascript" type="text/javascript">
		    tinyMCE.init({
		    // General options
			mode : "exact",
			elements : "page_body",
			plugins : "safari,pagebreak,table,save,advhr,advimage,emotions,iespell,inlinepopups,media,paste,fullscreen,visualchars,nonbreaking,blockquote",
			language : "$lang",
			convert_urls : false,
			relative_urls : false,
			apply_source_formatting : false,
			remove_linebreaks : true,
			gecko_spellcheck : true,
			spellchecker_languages : "+English=en,Dutch=nl,German=de,Spanish=es,Danish=dk,Swedish=sv,French=fr,Japanese=jp",
			//script should be a default element according to the docs, but doesn"t appear to work
			extended_valid_elements : "script[language|src|type],iframe[*]",
			//the following shouldn"t be necessary, but seems to be due to some sort of tinymce bug
			custom_elements : "script,iframe",
			
		    // Theme options
		    theme : 'advanced',
			theme_advanced_buttons1 : "fontselect,fontsizeselect,bold,italic,underline,strikethrough,sub,sup,separator,link,unlink,image,code,fullscreen",
		    theme_advanced_buttons2 : "justifyleft,justifycenter,justifyright,justifyfull,separator,outdent,indent,blockquote,separator,bullist,numlist,separator,forecolor,backcolor,separator,charmap,hr,removeformat,visualaid",
		    theme_advanced_buttons3 : "undo,redo,cut,copy,paste,pastetext,replace,separator,tablecontrols",
			theme_advanced_buttons4 : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,
			theme_advanced_source_editor_width : "500",
	    	theme_advanced_source_editor_height : "380"
		    
    });
	</script>\n
<SCRIPT>
    var rules=new Array();
    rules[0]='page_title|regexp|^([A-Za-z0-9-_ ()]+)$|The Page title can only contain the following characters: ()-_';
</SCRIPT>
<script language="javascript" type="text/javascript">
    function addFile(form) {
        if (form.weblog_add_file.selectedIndex != '') {
            form.page_body.value = form.page_body.value + '{{file:' + form.weblog_add_file.options[form.weblog_add_file.selectedIndex].value + '}}';    
			tinyMCE.execCommand('mceInsertContent',true,'{{file:' + form.weblog_add_file.options[form.weblog_add_file.selectedIndex].value + '}}');
		}
    }
</script>

END;

    // Restore the trailing slash in $url
	$url = url;
    
    $run_result = <<< END
    
<form method="post" name="elggform" action="{$url}_folio/action_redirection.php" onsubmit="return performCheck('elggform', rules, 'classic');">

	<h2>{$page_title}</h2>
END;

	$run_result .= templates_draw(array(
                                'context' => 'databoxvertical',
                                'name' => 'Title',
                                'contents' => "<input type=text id='page_title' value='$title' name='page_title' style='width: 80%' />"
								) );

    $run_result .= templates_draw(array(
                                'context' => 'databoxvertical',
                                'name' => '',
                                'contents' => display_input_field(array("page_body",stripslashes($body),"weblogtext"))
                            )
                            );

    if (logged_on) {
    	$sitename = sitename;
    	$filelist = viewfolder(-1, $_SESSION['userid'], 0);
    	$run_result .= <<< END
            <p>
      			Embed a file from your $sitename file storage:<br />
                <select name="weblog_add_file" id="weblog_add_file">
                    $filelist        
                </select>
                <input type="button" value="Add" onclick="addFile(this.form);" /><br />
                (This will add a code to your weblog post that will be converted into an embedded file.)
            </p>
    
END;

    }
    
//	if (! stripos($_SERVER['HTTP_USER_AGENT'],"Safari")) //no rich text in Safari - no worky
    $run_result .= <<< END
    <p>
      Embed an external video or other widget:<br />(Copy and paste embed code from external web site)<br />
                <span id="embed"><textarea name="weblog_embed_object" id="weblog_embed_object" rows="3" cols="40"></textarea>
                <input type="button" value="Embed" onclick="tinyMCE.execCommand('mceInsertRawHTML',true,this.form.weblog_embed_object.value);tinyMCE.execCommand('mceCleanup');" /></span>
            </p>
END;

	$run_result .= <<< END
	<p>
		<input type="hidden" name="action" value="folio:page:update" />
		<input type="hidden" name="username" value="{$username}" />
		<input type="hidden" name="user_ident" value="{$user_ident}" />
		<input type="hidden" name="page_ident" value="{$page_ident}" />
		<!-- <input type="hidden" name="parentpage_ident" value="{$parentpage_ident}" /> -->
		<input type="submit" value="Save" />
        <INPUT TYPE="button" VALUE="Cancel" onClick="javascript:history.back()">
	</p>

END;

	// Include security
	$run_result .= folio_control_page_edit_security( $page )
		. folio_control_page_edit_move( $page ) . '</form>';
    
    return $run_result;
}
        
?>
