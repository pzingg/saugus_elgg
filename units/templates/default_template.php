<?php
    global $CFG;
    global $template;
    global $template_definition;
    $sitename = $CFG->sitename;
    
    $template_definition[] = array(
                                    'id' => 'css',
                                    'name' => gettext("Stylesheet"),
                                    'description' => gettext("The Cascading Style Sheet for the template."),
                                    'glossary' => array(),
                                    'display'  => 1,
                                    );

    $template['css'] = <<< END
/*
Default CSS for the Elgg Learning Landscape
Version 0.65 - August 2006
Modified by Jim Klein - May 2008
*/

body { 
	margin:0;
	font-family:Verdana, "Myriad Web", Arial, Helvetica, sans-serif;
	color: #555;
	line-height:1.6em;
	font-size: 80%;
	margin:0;
	padding:0;
	background:#fff url({{url}}_templates/default_images/bg.gif);
}

p {
	color:#555;
}

a {
	text-decoration:none;
	color:#6EAE87;
}

blockquote a {
	color: #555;
}

#interests a {
	color: #555;
}

ol, ul {
	color:#555;
}

h2 {
	font-size:100%;
}

blockquote{
	background: #EEE;
	padding: 10px;
	padding-bottom: 40px;
	margin: 1em;
}

input {
	margin: 0 5px 5px 0;
	font-family:Verdana, Arial, Helvetica, sans-serif;
}

textarea {
	background-color: #FFF;
	color: #000;
	border: 1px solid #CCC;
	font-size: 1.4em;
	padding: 3px;
	margin: 0 0 5px;
	width:95%;
	height:100px;
}

/* WRAPS THE WHOLE PAGE */

#container {
/*  width:900px; */
	width:780px;
	padding:0;
	background:#fff;
	margin: 0px auto 0px auto;
	position: relative;
}

/* HEADER */

#header {
   margin:0 0 10px 0;
   text-align:left;
 }

#header #logo{
	margin: 0px;
    padding:20px 10px 10px;
	float:left;
}

#header #logo h1 {
    font-size:1.4em;
    padding:0;
    margin:0 0 5px 0;
}

#header #logo h1 a{
   color:#4391B0;
   padding:0;
   margin:0;
   font-family:Helvetica, "Myriad Web", Arial, sans-serif;
   font-size:1.4em;
}

#header #logo h2{
   color:#6EAE87;
   padding:0;
   margin:0;
   font-size:1.2em;
}

#header #welcome {
	float:  right;
	margin: 30px 30px 0 0;
}

#topnav {
	height: 20px;
	background: #bbb;
	font-weight: bold;
	min-width:780px;
}

#global_menuoptions{
	float:right;
	text-align:right;
	/*background:#fff;*/
	width:300px;
	padding: 0;
}

#global_menuoptions ul {
	margin:0;
}

#global_menuoptions li {
   margin: 0; 
    padding: 0;
    display: inline;
    list-style-type: none;
    border: none;
}

#global_menuoptions li a{
   text-decoration: none;
   padding:0 10px 0 0;
   color: #333;
       font-size:85%;
}

#global_menuoptions li a:hover {
	color: #C7D8D9;
}

#global_menuoptions p {
  padding:0 10px 0 0;
}

#search_header {
   text-align:right;
   margin:0 0 0 30px ;
}

/* WRAPS THE MAIN CONTENT PANE AND THE SIDEBAR */

#content_holder {
   padding:0px;
   margin:0px;
   width:100%;
   border-top:2px solid #eee;
 }

/* WRAPS THE MAIN CONTENT PANE - THE LEFT HAND SIDE OF THE SCREEN */

#maincontent_container {
   padding:0 0 0 20px;
 }

/* NAVIGATION */

#navigation {
    height: 19px;
    width: 450px;
    margin: 0 0 0 10px;
    padding: 0;
    text-align:left;
    float: left;
}

#navigation li {
    margin: 0; 
    padding: 0;
    display: inline;
    list-style-type: none;
    border: none;
}

#navigation a:link, #navigation a:visited {

    /*background: #eee;*/
    /*font-weight: normal;*/
    padding: 5px;
    margin: 0 2px 0 0;
    border: 0px solid #036;
    text-decoration: none;
    color: #333;
	font-size:85%;
}

/*#navigation a:link.selected, #navigation a:visited.selected {
    border-bottom: 1px solid #fff;
    background: #4391B0; /*#FCD63F;*/
    color: #fff; /*#393;*/
    font-weight: bold;
}*/

#navigation a:hover {
    color: #C7D8D9;
}

#navigation li a:hover{
       color: #C7D8D9;
    }

/* MISC */

.clearme{clear:both;}
 

/* SIDEBAR AND ITS CONTENTS */

#sidebar {
   background:#fff;
   margin:0 40px 0 0;
  }

/* ACTUAL SIDEBAR CONTENT */


#sidebar ul {
    margin: 0;
    padding: 0 10px;
    list-style: none;
}

#sidebar ul li {
    margin: 10px 0;
    padding-left: 0;
}

#sidebar ul li ul li{
    padding: 0 0 0 10px;
}

#sidebar h2 {
    font-family: "Lucida Grande", arial, sans-serif;
    font-weight: bold;
    color: #333;
    background:#fff;
    margin: 20px 0 3px 0;
    padding: 0;
    border: none;
}

#sidebar h2 {
    border-bottom: 0px solid #666;
	text-align:center;
    border-top:1px solid #777;
    color:#777;
	font-size:1em;
	width:200px;
}

#search_widget {
  background:url({{url}}_templates/default_images/own_ad_bg.gif) repeat-y;
  text-align:center;
  width:200px;
  margin:0 0 0 10px;
}

#search_widget p {
  margin:10px;
  font-weight:bold;
}

#search_widget input {
  margin:5px 0 5px 0;
}

/* SIDEBAR DISPLAY COMPONENTS */

#sidebar_user {
}

#recent_activity {
}

#community_owned {
}

#community_membership {
}

#sidebar_friends p {
    padding:0 0 0 10px;
}

#search {
}

#me {
    border:0px solid #086a89;
	background:#4391B0 url({{url}}_templates/default_images/login_bg.gif);
	width:200px;
	padding:0;
	margin:0;
	text-align:left;
	min-height:80px;
}

#me label {
    padding:4px; 
	margin:0 0 5px 0;
	color:#fff;
}

#me h2 {
    color:#fff;
	font-size:1.2em;
	padding:5px;
	margin:0 10px 0 10px;
	background:#0C5896;
}

#me #icon {
   margin:3px 0 0 4px;
   float: left; 
   width: 70px;
   padding:0;
}

#me #contents {
   margin: 0 0 0 75px;
   text-align: left;
   padding:0;
}

#me a {
  color:#fff;
  text-decoration:underline;
}

#me p {
  color:#fff;
  margin:0;
  padding:0 0 5px 0;
}

/* Two new divs to let put curved images around the 'me' box */

#me_top {
  background:url({{url}}_templates/default_images/login_top.gif) bottom no-repeat;
  height:9px;
  width:200px;
  padding:0;
  margin:0;
}

#me_bottom {
  background:url({{url}}_templates/default_images/login_bottom.gif) no-repeat;
  height:9px;
  width:200px;
  margin:0;
  padding:0;
}

/* extra div's when looking at someone else's page */

#sidebar_weblog {
}

#sidebar_files {
}


 #sidebar_user h2 {
    display:none;
}

/* FOOTER */

#footer {
    background:#fff;
    text-align: center;
    padding:0;
	border-top:1px solid #ccc;
	border-bottom:1px solid #ccc;
	font-size:0.8em;
	width: 45em;
	margin:20px 0 0 0; /* 10 auto; */
}

#footer a:hover {
   text-decoration:underline;
 }

#footer a:link, #footer a:visited {
    text-align:right;
}

/* Remove the search component from the sidebar - it is replaced by the search widget */

#search {
   display:none;
}

/* SYSTEM MESSAGES  */

#system_message{ 
    border:1px solid #D3322A;
    background:#F7DAD8;
    color:#000;
    padding:3px 50px;
    margin:20px 20px 0 0;
}

#system_message p{
   padding:0px;
   margin:2px;
 }

/* THIS DISPLAYS THE ACTUAL CONTENT WITHIN maincontent_container */

#maincontent_display {
    margin: 0;
    padding: 20px 0 20px 0;
    width: 95%;
    text-align: left;
    float: left;
    background-color: #fff;
    color:#000;
}

#maincontent_display h1 {
    padding-bottom: 2px;
    border-bottom: 1px solid #666;
    margin: 0;
    font-size:130%;
    color: #666;
    background-color: #fff;
}

#maincontent_display a {
    /*color:#6EAE87;*/
}

#maincontent_display a:hover {
    text-decoration:underline;
}



/* SUB MENU attributes */

#maincontent_display #sub_menu {
    font-family: verdana;
    padding: 0px;
    margin: 5px 0 20px 0;
    color: #000;
    background-color:#fff;
}

#maincontent_display #sub_menu a {
    font-weight:bold;
    margin:0px;
    padding:0px;
    color:#777;
}

#maincontent_display #sub_menu a:hover {
    text-decoration: underline;
}

#maincontent_display #sub_menu p {
      margin:0px;
      padding:0px;
}

/* INDIVIDUAL BLOG POSTS */


/* wraps all blog components */

.weblog_posts {
   margin:0 0 15px 0;
}

.weblog_posts .entry h3 {
   color:#1181AA;
   background:#fff;
   padding: 0 0 10px 110px;
   font-size:1em;
}

.weblog_title h3 {
   padding:0;
   margin:0;
 }

.user {
    float: left;
    margin: 0px;
    padding:0 0 5px 0;
    width: 105px;
    text-align: left;
}

.user img {
   border:2px solid #6EAE87;
}


.user a {
    
}

.post {
    margin: 0 0 10px 0;
    padding: 0 0 20px 110px;
    font-family: arial;
}

.post a {
  color:#6EAE87;
}

.post p {
    padding: 0;
    margin: 3px 0 10px 0;
    line-height: 16px;
    color:#555;
}

.post ol, .post ul {
    margin: 3px 0 10px 0;
    padding: 0;
}

.post li {
    margin: 0 0 0 30px;
    line-height: 16px;
}

.post ul li {
    list-style-type: square;
}

.post .blog_edit_functions p {
      margin:10px 0 0 0;
      color:#777;
}

.post .blog_edit_functions a {
         
}

.post .weblog_keywords p {
     margin:20px 20px 0 20px;
     color:#6EAE87;
     font-weight:bold;
}

.post .weblog_keywords a {
     text-decoration:underline;
     color:#6EAE87;
}

.info p {
    padding: 4px;
    margin: 20px 0 5px 0;
    color: #666;
    background:#fff;
    font-family: verdana;
    font-weight: normal;
    line-height: 14px;
    text-align: left;
     border-left:4px solid #eee;
   border-right:4px solid #eee;
   border-top:1px solid #eee;
   border-bottom:1px solid #eee;
}

.info p a {
    color: #666;
    background:#fff url({{url}}_templates/default_images/comment_add.gif) left no-repeat;
    text-decoration: none;
    /*border-bottom: 1px dotted #666;*/
    padding-bottom: 0;
    padding:0 0 0 20px;
}

.external_services p {
    padding: 2px;
    margin: 20px 0 5px 0;
    color: #666;
    background:#fff;
    font-family: verdana;
    font-weight: normal;
      text-align: left;
     border-left:4px solid #eee;
   border-right:4px solid #eee;
   border-top:1px solid #eee;
   border-bottom:1px solid #eee;
}


#comments ol, #comments ul {
    margin: 3px 0 10px 0;
    padding: 0;
}

#comments a {
  color:#6EAE87;
}

#comments li {
    margin: 10px 0 10px 30px;
    line-height: 16px;
}

#comments ul li {
    list-style-type: square;
}

#comments h4 {
    /*color:#1181AA;*/
    background:url({{url}}_templates/default_images/comments.gif) left no-repeat;
    padding:0 0 0 20px;
}

.comment_owner {
    border:1px solid #eee;
	background:#f2f7fb;
	padding:5px;
    margin:20px 0 5px 0;
   height:50px;
}

.comment_owner img {
   margin:0px 5px 0px 0px;
   text-align:left;
}

.comment_owner a {
   background:#f2f7fb;
}

.comment_owner p {
  padding:0;
  margin:0;
}

.weblog_dateheader {
    padding: 0;
    margin: 0 0 5px 0;
    color: #ccc; /*#333;*/
    background:#fff;
    font-weight: normal;
    font-style: italic;
    line-height: 12px;
    border:0px;
    border-bottom: 1px solid #ccc;
   text-align:right;
}

.weblogdateheader {
    padding: 0;
    margin: 0 0 5px 0;
    color: #ccc; /*#333;*/
    background:#fff;
    font-weight: normal;
    font-style: italic;
    line-height: 12px;
    border:0px;
    border-bottom: 1px solid #ccc;
   text-align:right;
   font-size:0.9em;
}


/* YOUR RESOURCES */

.feeds {
  border-bottom: 1px dotted #aaaaaa;
}

.feed_content a {
      color:#6EAE87;
    border:0px;
 }

.feed_content p {
   padding:0 0 10px 0;
}

.feed_content a:hover{
    background:#fff;
    }

.feed_content img {
  border: 1px solid #666666;
  padding:5px;
}

.feed_content h5 {
   font-size:1.2em;
   padding:0;
   margin:0 0 10px 0;
}


.feed_content h3 {
      padding:0 0 4px 0;
      margin:0px;
}

.feed_content h3 a{
     color:black;
     border:0px;
     border-bottom:1px;
     border-style:dotted;
     border-color:#eee;
}

.feed_content h3 a:hover{
    background:#FCD63F;
       color:#000;
    }

.feed_date h2 {
    font-size:13px;
    line-height: 21px;
  font-weight: bold;
  padding: 5px 10px 5px 5px;
  background: #C7D8D9;
  color:#000;
  text-decoration:none;
}

.via {
   border-top:1px solid #eee;
   border-bottom:1px solid #eee;
   border-left:4px solid #eee;
   border-right:4px solid #eee;
   margin:20px;
}

.via a {
    font-size:80%;
    color:#1181AA;
    background:#fff;
}

.via a:hover {
    background:#ffc;
    color:#1181AA;
}


/*
DIV's to help control look and feel - infoholder holds all the profile data
and is always located in within 'maincontentdisplay'
*/

/* holds profile data */
.infoholder {
    border:1px;
    border-color:#eee;
    border-style:solid;
    margin:0 0 5px 0;
}

.infoholder p {
   padding:0 0 0 5px;
   margin:15px;
}

.infoholder a {
   color:#6EAE87;
}

.infoholder a:hover {
   text-decoration:underline;
}

.infoholder .fieldname h2 {
          border:0;
          border-bottom:1px;
          border-color:#eee;
          border-style:solid;
          padding:3px;
		  margin:0;
          color:#666;
          background:#fff;
}   

.infoholder_twocolumn {
    padding:4px;
    border:1px;
    border-color:#eee;
    border-style:solid;
    margin:0 0 10px 0;
 }

.infoholder_twocolumn .fieldname h3{
    color:#666;
    background:#fff;
    border:0px;
    border-bottom:1px;
    border-color:#eee;
    border-style:solid;
}

.infoholder_twocolumn h3 {
    font-size:1.1em;
    padding:0;
    margin:0;
}

/* holds administration data */

.admin_datatable {
  border:1px;
  border-color:#eee;
  border-style:solid;
  margin:0 0 5px 0;
}

.admin_datatable p {
     padding:0px;
     margin:0px;
}

.admin_datatable a {
   
}


.admin_datatable td {
   text-align:left;
   padding:0;
   margin:0;
}

.admin_datatable h3{
     color:#666;
     background:#fff;
     padding:0;
     margin:0;
     font-size:1em;
}

.admin_datatable h4{
     color:#666;
     background:#fff;
     padding:0;
     margin:0;
     font-size:1em;
}

.admin_datatable h4 {
}

/* header plus one row of content */

.databox_vertical {
   background-color: #F9F9F9;
   color:#000;
   border:1px;
   border-style:solid;
   border-color:#DDD;
   margin:15px 0 5px 0;
   padding:5px;
 }

 #maincontent_display .databox_vertical p{
   padding:0;
   margin:0 0 10px 0;
   color:#1181AA;
   background-color: #F9F9F9;
 }

.databox_vertical .fieldname h3 {
  padding:0px;
  margin:0 0 15px 0;
  color:#1181AA;
  background-color: #F9F9F9;
  font-size:1.2em;
}

/* holds file content */

.filetable {
   background-color: #F9F9F9;
   color:#000;
   border:1px;
   border-style:solid;
   border-color:#DDD;
   margin:0 0 5px 0;
   width:100%;
 }

 .filetable p{
   padding:0px;
   margin:0px;
   color:#000; /*#1181AA;*/
   background-color: #F9F9F9;
 }

.filetable a{
   background-color: #F9F9F9;
 }


.filetable table {
    text-align:left;
}

#edit_files h4 {
     
}
  

/* holds fodler content */

.foldertable {
   background-color: #F9F9F9;
   color:#000;
   border:1px;
   border-style:solid;
   border-color:#DDD;
   margin:0 0 5px 0;
   width:100%;
 }

.foldertable a{
  background-color: #F9F9F9;
 }

 .foldertable p{
   padding:0px;
   margin:0px;
   color:#1181AA;
   background-color: #F9F9F9;
 }

.foldertable table {
    text-align:left;
}

/* holds network data */

.networktable {
   
}

#embed textarea {
	width: 80%;
	height: 60px;
}

#embed input {
	vertical-align: top;
}

.addthis_button a {
	margin:0;
	padding:0 2px;
	border: none;
	text-decoration: none;
	background: none;
}

.addthis_button a img {
	vertical-align: text-bottom;
}

.addthis_button a:visited,.addthis_button a:link {
	margin:0;
	padding:0;
	background: none;
}

#addthis_services a {
	text-decoration:none;
	border:none;
}

END;

    $template_definition[] = array(
                                    'id' => 'pageshell',
                                    'name' => gettext("Page Shell"),
                                    'description' => gettext("The main page shell, including headers and footers."),
                                    'display' => 1,
                                    'glossary' => array(
                                                            '{{metatags}}' => gettext("Page metatags (mandatory) - must be in the 'head' portion of the page"),
                                                            '{{title}}' => gettext("Page title"),
                                                            '{{menu}}' => gettext("Menu"),
                                                            '{{topmenu}}' => gettext("Status menu"),
                                                            '{{mainbody}}' => gettext("Main body"),
                                                            '{{sidebar}}' => gettext("Sidebar")
                                                        )
                                    );
    
    $welcome = gettext("Welcome"); // gettext variable
    
    $template['pageshell'] = <<< END
    
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{{title}}</title>
{{metatags}}
</head>
<body>
<div id="topnav">
	<ul id="navigation">
		{{menu}}
		<li></li><!-- used to validate -->
	</ul>
	<div id="global_menuoptions"><!-- open div global_menuoptions -->
		{{topmenu}}
	</div><!-- close div global_menuoptions -->
</div>
<!-- elgg banner and logo -->

<div id="container">
	<table cellpadding="0" cellspacing="0" width="100%" border="0" bgcolor="#FFFFFF">
		<tr>
			<td>
				<div id="header"><!-- open div header -->
				        <div id="logo"><!-- open div logo -->
					    <h1><a href="{{url}}index.php">$sitename</a></h1>
					    <h2>Personal Learning Landscape</h2>
					</div><!-- close div logo -->
					<p id="welcome">Welcome {{userfullname}} </p>
				</div><!-- close div header -->
				<div class="clearme"/>
			</td>
		</tr>
		<tr>
			<td>
				<div id="content_holder">
					<table cellpadding="3" cellspacing="0" border="0" width="100%">
					    <tr>
							<td valign="top" id="maincontent_container" align="left">
					            {{messageshell}}
					            {{mainbody}}
					        </td>
							<td width="220px" valign="top" id="sidebar" align="left">
					        	<ul><!-- open sidebar lists -->
					                {{sidebar}}
					        	</ul><!-- close sidebar lists -->
								<div id="search_widget"><!-- open div search_widget -->
								<img src="{{url}}_templates/default_images/own_ad_top.gif" alt="" border="0" />
					            <form name="searchform" action="{{url}}search/all.php">
							 		<script language="JavaScript" type="text/javascript">
										<!--
										function submitthis()
										{
										  document.searchform.submit() ;
										}
										-->
									</script>
						            <p><label>Search</label><br /><input type="text" size="20" name="tag" value="" /> <select name=""><option value="all">--- all ---</option></select> <br /><input type="submit" name="" value="search" /></p>
					            </form>
							<img src="{{url}}_templates/default_images/own_ad_bottom.gif" alt="" border="0" />
							</div><!-- close div search_widget -->
						</td>
					   </tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td align="center">
				<div id="footer"><!-- start footer -->
					<span style="color:#FF934B">$sitename</span> <a href="{{url}}content/terms.php">Terms and conditions</a>&nbsp;|&nbsp;<a href="{{url}}content/guidelines.php">Usage guidelines</a>&nbsp;|&nbsp;<a href="{{url}}content/privacy.php">Privacy policy</a>
				</div><!-- end footer --><br />
				<a href="http://elgg.org"><img src="{{url}}_templates/elgg_powered.png" title="Elgg powered" border="0" alt="Elgg powered logo" /></a>
				<a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-sa/3.0/80x15.png" /></a>
				<br />
			</td>
		</tr>
	</table>
</div>
</body>
</html>            

END;

    $template_definition[] = array(
                                    'id' => 'contentholder',
                                    'name' => gettext("Content holder"),
                                    'description' => gettext("Contains the main content for a page (as opposed to the sidebar or the title)."),
                                    'glossary' => array(
                                                            '{{title}}' => gettext("The title"),
                                                            '{{submenu}}' => gettext("The page submenu"),
                                                            '{{body}}' => gettext("The body of the page")
                                                        )
                                    );    

    $template['contentholder'] = <<< END
    
    <div id="maincontent_display">

    <h1>{{title}}</h1>
    {{submenu}}
    {{body}}
      </div>
    
END;

$template_definition[] = array(
                                    'id' => 'sidebarholder',
                                    'name' => gettext("Sidebar section holder"),
                                    'description' => gettext("Contains the sidebar section titles"),
                                    'glossary' => array(
                                                            '{{title}}' => gettext("The header"),
                                                                                                       '{{body}}' => gettext("The body of the page")
                                                            
                                                        )
                                    );

    $template['sidebarholder'] = <<< END
 
    <h2>{{title}}</h2>
       {{body}}

END;

    $template_definition[] = array(
                                    'id' => 'ownerbox',
                                    'name' => gettext("Owner box"),
                                    'description' => gettext("A box containing a description of the owner of the current profile."),
                                    'glossary' => array(
                                                            '{{name}}' => gettext("The user's name"),
                                                            '{{profileurl}}' => gettext("The URL of the user's profile page, including terminating slash"),
                                                            '{{usericon}}' => gettext("The user's icon, if it exists"),
                                                            '{{tagline}}' => gettext("A short blurb about the user"),
                                                            '{{usermenu}}' => gettext("Links to friend / unfriend a user"),
                                                            '{{lmshosts}}' => gettext("Links to any lms hosts the user is attached to"),

                                                        )
                                    );

    $tags = gettext("Tags");
    $resources = '';
    if ($CFG->resources_enabled)
    	$resources = ' | <a href="{{profileurl}}newsclient/">'.gettext("Resources").'</a>';
    $template['ownerbox'] = <<< END
    
     <div id="me">
        <div id="me_top"><!-- to let IE size properly --></div>
        <div id="icon"><a href="{{profileurl}}">{{usericon}}</a></div>
        <div id="contents" >
          <p>
            <span class="userdetails">{{name}}<br /><a href="{{profileurl}}rss/">RSS</a> | <a href="{{profileurl}}tags/">$tags</a>$resources</span></p>
            <p>{{tagline}}</p>
            <p>{{lmshosts}}</p>
            <p class="usermenu">{{usermenu}}</p>
        </div>
        <div id="me_bottom"><!-- this comment is for IE sizing issues --></div>
       </div>

END;
                                    
    $template_definition[] = array(
                                    'id' => 'messageshell',
                                    'name' => gettext("System message shell"),
                                    'description' => gettext("A list of system messages will be placed within the message shell."),
                                    'glossary' => array(
                                                            '{{messages}}' => gettext("The messages")
                                                        )
                                    );

    $template['messageshell'] = <<< END
    
    <div id="system_message">{{messages}}</div><br />
    
END;

    $template_definition[] = array(
                                    'id' => 'messages',
                                    'name' => gettext("Individual system messages"),
                                    'description' => gettext("Each individual system message."),
                                    'glossary' => array(
                                                            '{{message}}' => gettext("The system message")
                                                        )
                                    );

    $template['messages'] = <<< END

    <p>
        {{message}}
    </p>    
    
END;
    

    $template_definition[] = array(
                                    'id' => 'menu',
                                    'name' => gettext("Main menu shell"),
                                    'description' => gettext("A list of main menu items will be placed within the menubar shell."),
                                    'glossary' => array(
                                                            '{{menuitems}}' => gettext("The menu items")
                                                        )
                                    );

    $template['menu'] = <<< END
    
        {{menuitems}}
END;

    $template_definition[] = array(
                                    'id' => 'menuitem',
                                    'name' => gettext("Individual main menu item"),
                                    'description' => gettext("This is the template for each individual main menu item. A series of these is placed within the menubar shell template."),
                                    'glossary' => array(
                                                            '{{location}}' => gettext("The URL of the menu item"),
                                                            '{{name}}' => gettext("The menu item's name")
                                                        )
                                    );

    $template['menuitem'] = <<< END
    
    <li><a href="{{location}}">{{name}}</a></li>
    
END;

$template_definition[] = array(
                                    'id' => 'selectedmenuitem',
                                    'name' => gettext("Selected individual main menu item"),
                                    'description' => gettext("This is the template for an individual main menu item if it is selected."),
                                    'glossary' => array(
                                                            '{{location}}' => gettext("The URL of the menu item"),
                                                            '{{name}}' => gettext("The menu item's name")
                                                        )
                                    );

    $template['selectedmenuitem'] = <<< END
    
    <li><a class="current" href="{{location}}">{{name}}</a></li>
    
END;

    $template_definition[] = array(
                                    'id' => 'submenu',
                                    'name' => gettext("Sub-menubar shell"),
                                    'description' => gettext("A list of sub-menu items will be placed within the menubar shell."),
                                    'glossary' => array(
                                                            '{{submenuitems}}' => gettext("The menu items")
                                                        )
                                    );

    $template['submenu'] = <<< END
    
        <div id="sub_menu">
        <p>
            {{submenuitems}}
        </p>
        </div>
END;

    $template_definition[] = array(
                                    'id' => 'submenuitem',
                                    'name' => gettext("Individual sub-menu item"),
                                    'description' => gettext("This is the template for each individual sub-menu item. A series of these is placed within the sub-menubar shell template."),
                                    'glossary' => array(
                                                            '{{location}}' => gettext("The URL of the menu item"),
                                                            '{{menu}}' => gettext("The menu item's name")
                                                        )
                                    );

    $template['submenuitem'] = <<< END
    
    <a href="{{location}}">{{name}}</a>&nbsp;|
    
END;

    $template_definition[] = array(
                                    'id' => 'topmenu',
                                    'name' => gettext("Status menubar shell"),
                                    'description' => gettext("A list of statusbar menu items will be placed within the status menubar shell."),
                                    'glossary' => array(
                                                            '{{topmenuitems}}' => gettext("The menu items")
                                                        )
                                    );

    $template['topmenu'] = <<< END
    
        <ul id="global_menuoptions">
            {{topmenuitems}}
        </ul>

END;

$template_definition[] = array(
                                    'id' => 'topmenuitem',
                                    'name' => gettext("Individual statusbar menu item"),
                                    'description' => gettext("This is the template for each individual statusbar menu item. A series of these is placed within the status menubar shell template."),
                                    'glossary' => array(
                                                            '{{location}}' => gettext("The URL of the menu item"),
                                                            '{{menu}}' => gettext("The menu item's name")
                                                        )
                                    );

    $template['topmenuitem'] = <<< END
    
    <li><a href="{{location}}">[{{name}}]</a></li>
    
END;

    $template_definition[] = array(
                                    'id' => 'databox',
                                    'name' => gettext("Data input box (two columns)"),
                                    'description' => gettext("This is mostly used whenever some input is taken from the user. For example, each of the fields in the profile edit screen is a data input box."),
                                    'glossary' => array(
                                                            '{{name}}' => gettext("The name for the data we're inputting"),
                                                            '{{column1}}' => gettext("The first item of data"),
                                                            '{{column2}}' => gettext("The second item of data")
                                                        )
                                    );

    $template['databox'] = <<< END

<div class="infoholder_twocolumn">
        <div class="fieldname">
            <h3>{{name}}</h3>
        </div>
        <p>{{column1}}</p>
        <p>{{column2}}</p>
    </div>
        
END;

    $template_definition[] = array(
                                    'id' => 'databox1',
                                    'name' => gettext("Data input box (one column)"),
                                    'description' => gettext("A single-column version of the data box."),
                                    'glossary' => array(
                                                            '{{name}}' => gettext("The name of the data we're inputting"),
                                                            '{{column1}}' => gettext("The data itself")
                                                        )
                                    );

    $template['databox1'] = <<< END

<div class="infoholder">
        <div class="fieldname">
            <h2>{{name}}</h2>
        </div>
        <p>{{column1}}</p>
    </div>
        
END;

$template_definition[] = array(
                                    'id' => 'adminTable',
                                    'name' => gettext("adminTable"),
                                    'description' => gettext("This table is used to house stats and administration details until a good CSS solution can be applied."),
                                    'glossary' => array(
                                                            '{{name}}' => gettext("Column One"),
                                                            '{{column1}}' => gettext("Column Two"),
                                                            '{{column2}}' => gettext("Column Three")
                                                        )
                                    );

    $template['adminTable'] = <<< END

<div class="admin_datatable">
    <table width="80%">
    <tr>
        <td width="25%" valign="top">
            {{name}}
        </td>
        <td width="45%" valign="top">
            {{column1}}
        </td>
        <td width="30%" valign="top">
            {{column2}}
        </td>
    </tr>
    </table>
</div>

END;

$template_definition[] = array(
                                    'id' => 'flagContent',
                                    'name' => gettext("flagContent"),
                                    'description' => gettext("This holds the flag content function throughout Elgg"),
                                    'glossary' => array(
                                                            '{{name}}' => gettext("Column One"),
                                                            '{{column1}}' => gettext("Column Two"),
                                                            '{{column2}}' => gettext("Column Three")
                                                        )
                                    );

    $template['flagContent'] = <<< END

<div class="flagcontent">
    {{name}}
    {{column1}}
    {{column2}}
</div>

END;

    $template_definition[] = array(
                                    'id' => 'databoxvertical',
                                    'name' => gettext("Data input box (vertical)"),
                                    'description' => gettext("A slightly different version of the data box, used on this edit page amongst other places."),
                                    'glossary' => array(
                                                            '{{name}}' => gettext("Name of the data we\'re inputting"),
                                                            '{{contents}}' => gettext("The data itself")
                                                        )
                                    );

    $template['databoxvertical'] = <<< END
<div class="databox_vertical">
        <div class="fieldname">
            <h3>{{name}}</h3>
        </div>
        <p>{{contents}}</p>
    </div>
        
END;

?>