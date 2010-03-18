<?php
/*
* Created a dynamic css file to fix problem with absolute file references.
* Uses a apache mod rewrite rule to have the server convert tree.css into tree.php
*
* @package folio
* @TODO: Possible performance increase by not loading all of includes.php for $CFG->wwwroot variable.
*/
    require_once '../../../includes.php';
    $path = $CFG->wwwroot . "mod/folio/control/yui/build/treeview/assets";

    
    header('Content-type: text/css');
    // Force the browser to reload after two hours of using this css sheet.
    header('Cache-control: must-revalidate');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 7200) . ' GMT');

    
    print <<<_CSS
/* Copyright (c) 2006 Yahoo! Inc. All rights reserved. */

/* first or middle sibling, no children */
.ygtvtn {
	width:16px; height:22px; 
	background: url({$path}/tn.gif) 0 0 no-repeat; 
}

/* first or middle sibling, collapsable */
.ygtvtm {
	width:16px; height:22px; 
	cursor:pointer ;
	background: url({$path}/tm.gif) 0 0 no-repeat; 
}

/* first or middle sibling, collapsable, hover */
.ygtvtmh {
	width:16px; height:22px; 
	cursor:pointer ;
	background: url({$path}/tmh.gif) 0 0 no-repeat; 
}

/* first or middle sibling, expandable */
.ygtvtp {
	width:16px; height:22px; 
	cursor:pointer ;
	background: url({$path}/tp.gif) 0 0 no-repeat; 
}

/* first or middle sibling, expandable, hover */
.ygtvtph {
	width:16px; height:22px; 
	cursor:pointer ;
	background: url({$path}/tph.gif) 0 0 no-repeat; 
}

/* last sibling, no children */
.ygtvln {
	width:16px; height:22px; 
	background: url({$path}/ln.gif) 0 0 no-repeat; 
}

/* Last sibling, collapsable */
.ygtvlm {
	width:16px; height:22px; 
	cursor:pointer ;
	background: url({$path}/lm.gif) 0 0 no-repeat; 
}

/* Last sibling, collapsable, hover */
.ygtvlmh {
	width:16px; height:22px; 
	cursor:pointer ;
	background: url({$path}/lmh.gif) 0 0 no-repeat; 
}

/* Last sibling, expandable */
.ygtvlp { 
	width:16px; height:22px; 
	cursor:pointer ;
	background: url({$path}/lp.gif) 0 0 no-repeat; 
}

/* Last sibling, expandable, hover */
.ygtvlph { 
	width:16px; height:22px; cursor:pointer ;
	background: url({$path}/lph.gif) 0 0 no-repeat; 
}

/* Loading icon */
.ygtvloading { 
	width:16px; height:22px; 
	background: url({$path}/loading.gif) 0 0 no-repeat; 
}

/* the style for the empty cells that are used for rendering the depth 
 * of the node */
.ygtvdepthcell { 
	width:16px; height:22px; 
	background: url({$path}/vline.gif) 0 0 no-repeat; 
}

.ygtvblankdepthcell { width:16px; height:22px; }

/* the style of the div around each node */
.ygtvitem { }  

/* the style of the div around each node's collection of children */
.ygtvchildren { }  
* html .ygtvchildren { height:2%; }  

/* the style of the text label in ygTextNode */
.ygtvlabel, .ygtvlabel:link, .ygtvlabel:visited, .ygtvlabel:hover { 
	margin-left:2px;
	text-decoration: none;
}

.ygtvspacer { height: 10px; width: 10px; margin: 2px; }

_CSS;

?>