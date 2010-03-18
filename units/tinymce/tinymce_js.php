<?php
    // Language setting

    global $CFG;
    global $page_owner;

//    if ((run('userdetails:editor', $page_owner) == "yes") && (! stripos($_SERVER['HTTP_USER_AGENT'],"Safari"))) {
	if (run('userdetails:editor', $page_owner) == "yes") {
    
        if (empty($CFG->userlocale)) {
            $lang = substr($CFG->defaultlocale, 0, 2);
        } elseif (is_array($CFG->userlocale)) {
            $lang = substr($CFG->userlocale[0], 0, 2);
        } else {
            $lang = substr($CFG->userlocale, 0, 2);
        }
        
        // Loose the trailing slash
        $url = substr($CFG->wwwroot, 0, -1);

        global $metatags;

        $metatags .= <<< END
<script language="javascript" type="text/javascript" src="$url/_tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
	tinyMCE.init({
		// General options
		mode : 'exact',
		elements : 'new_weblog_post,new_weblog_comment,profiledetails__biography__',
		plugins : 'safari,pagebreak,table,save,advhr,advimage,emotions,iespell,inlinepopups,media,paste,fullscreen,visualchars,nonbreaking,blockquote',
		pagebreak_separator : '{{more}}',
		language : '$lang',
		convert_urls : false,
		relative_urls : false,
		apply_source_formatting : false,
		remove_linebreaks : true,
		gecko_spellcheck : true,
		//script should be a default element according to the docs, but doesn't appear to work
		extended_valid_elements : 'script[language|src|type],iframe[*]',
		//the following shouldn't be necessary, but seems to be due to some sort of tinymce bug
		custom_elements : 'script,iframe',
		 
		// Theme options
		theme : 'advanced',
		theme_advanced_buttons1 : 'bold,italic,underline,strikethrough,justifyleft,justifycenter,justifyright,justifyfull,blockquote,bullist,numlist,pagebreak,separator,undo,redo,pastetext,separator,link,unlink,image,code,fullscreen',
		theme_advanced_buttons2 : '',
		theme_advanced_buttons3 : '',
		theme_advanced_buttons4 : '',
		theme_advanced_toolbar_location : 'top',
		theme_advanced_toolbar_align : 'left',
		theme_advanced_statusbar_location : 'bottom',
		theme_advanced_resizing : true,
		theme_advanced_source_editor_width : "500",
    	theme_advanced_source_editor_height : "380"
		
	});
</script>\n

END;
    }
?>
