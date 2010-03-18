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

    $template['css'] = file_get_contents($CFG->templatesroot . "Default_Template/css");

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
    
    $template['pageshell'] = file_get_contents($CFG->templatesroot . "Default_Template/pageshell");

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
    $resources = gettext("Resources");
    $template['ownerbox'] = <<< END
    
     <div id="me">
        <div id="me_top"><!-- to let IE size properly --></div>
        <div id="icon"><a href="{{profileurl}}">{{usericon}}</a></div>
        <div id="contents" >
          <p>
            <span class="userdetails">{{name}}<br /><a href="{{profileurl}}rss/">RSS</a> | <a href="{{profileurl}}tags/">$tags</a> | <a href="{{profileurl}}newsclient/">$resources</a></span></p>
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