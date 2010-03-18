<?php

	//	ELGG manage owned users page
		global $CFG;

	// Run includes
		require_once(dirname(dirname(__FILE__))."/includes.php");
		
	// Initialise functions for user details, icon management and profile management
		run("userdetails:init");
		run("profile:init");
		run("friends:init");
		run("ownedusers:init");
		
		define("context", "network");
		templates_page_setup();

	// Whose owned users are we looking at?
		global $page_owner;
		$profile_id = optional_param('profile_id');
	// You must be logged on to view this!
		protect(1);
		
		$title = run("profile:display:name") . " :: " . gettext($CFG->owned_users_caption);
								
		echo templates_page_draw( array(
                    $title, templates_draw(array(
                        'context' => 'contentholder',
                        'title' => $title,
                        'body' => run("ownedusers:move",array($page_owner,$profile_id))
                    )
                    )
                )
                );

?>