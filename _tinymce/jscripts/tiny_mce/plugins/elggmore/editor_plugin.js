/**
 * $RCSfile: editor_plugin_src.js,v $
 * $Revision: 1.22 $
 * $Date: 2006/02/10 16:29:39 $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2006, Moxiecode Systems AB, All rights reserved.
 */

/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('elggmore', 'en');

var TinyMCE_ElggMorePlugin = {
	getInfo : function() {
		return {
			longname : 'Insert date/time',
			author : 'Moxiecode Systems',
			authorurl : 'http://tinymce.moxiecode.com',
			infourl : 'http://tinymce.moxiecode.com/tinymce/docs/plugin_elggmoretime.html',
			version : tinyMCE.majorVersion + "." + tinyMCE.minorVersion
		};
	},
	
	initInstance : function(inst) {

		// Register custom keyboard shortcut
		inst.addShortcut('ctrl', 't', 'lang_elggmore_desc', 'mceElggMore');
	},


	/**
	 * Returns the HTML contents of the elggmore, inserttime controls.
	 */
	getControlHTML : function(cn) {
		switch (cn) {
			case "elggmore":
				return tinyMCE.getButtonHTML(cn, 'lang_elggmore_desc', '{$pluginurl}/images/more.gif', 'mceElggMore');

		}

		return "";
	},

	/**
	 * Executes the mceElggMore command.
	 */
	execCommand : function(editor_id, element, command, user_interface, value) {
		// Handle commands
		switch (command) {
			case "mceElggMore":
				tinyMCE.execInstanceCommand(editor_id, 'mceInsertContent', false, "{{more}}");
				return true;

		}

		// Pass to next handler in chain
		return false;
	}
};

tinyMCE.addPlugin("elggmore", TinyMCE_ElggMorePlugin);
