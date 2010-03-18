var req;
var reqComplete;
var errMSG;

window.onload = initAll;

function initAll() {
	var allLinks = document.getElementsByTagName("a");
	for (var i=0;i < allLinks.length; i++) {
		if (allLinks[i].className.indexOf("actionLink") > -1) {
			allLinks[i].onclick = doAction;
		}
	}
}

function xh_newRequest() {
	// CONSTRUCTOR for basic XMLHttpRequest object 'xh_Request'
	// NOTE: DO NOT USE 'new xh_newRequest()'
	
	// instantiate an XMLHttpRequest object; different for IE vs. Mozilla
	// try IE first - source: http://jibbering.com/2002/4/httprequest.html
	var xmlhttp=false;
	/*@cc_on @*/
	/*@if (@_jscript_version >= 5)
	// JScript gives us Conditional compilation, we can cope with old IE versions.
	// and security blocked creation of the objects.
	try {
	xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
	try {
	 xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	} catch (E) {
	 xmlhttp = false;
	}
	}
	@end @*/
	
	if (!xmlhttp && (typeof XMLHttpRequest != 'undefined')) xmlhttp = new XMLHttpRequest(); // Mozilla
	return xmlhttp;
}

function xh_statusMsg(state) {
	var msg = state;
	switch (state) {
		case 0: {msg = "Uninitialized"; break;}
		case 1: {msg = "Loading..."; break;}
		case 2: {msg = "Loaded"; break;}
		case 3: {msg = "Interactive"; break;}
		case 4: {msg = "Complete"; break;}
	}
	return msg;
}

function requestError() {
	var resp = (req.status == 200) ? req.responseText : "Problem: " + req.statusText;
	errMSG.innerHTML = resp.replace(/\n/gi,"<br />");
}

function doAction() {
	var action = this.href.substring(this.href.indexOf("action=")+7,this.href.indexOf("&comment_ident"));
	var comment_ident = this.href.substring(this.href.indexOf("&comment_ident=")+15,this.href.indexOf("&starttime"));
	errMSG = document.getElementById("err");
	errMSG.innerHTML = "";
	
	url = "ajax_action.php?action="+action+"&comment_ident="+comment_ident;

	try {
		req = xh_newRequest(); // instantiate a XMLHttpRequest object
		req.open("GET", url, true);

		req.onreadystatechange = function() {
			if (req.readyState == 4)
				
				if (req.responseText) {
					bodytext = req.responseText;
					if (bodytext != "") {
						document.getElementById("comment"+comment_ident).innerHTML = bodytext;
					}
					else alert(bodytext);
				}
				else requestError();
				
			//else errMSG.innerHTML = xh_statusMsg(req.readyState);
		} // onreadystatechange
	
		req.send(null);
	}
	catch(e) {
		errMSG.innerHTML = "<b>ERROR:</b> " + (e.message ? e.message : e);
	}
	
	return false;
}