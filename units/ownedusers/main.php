<?php

	/*
	*	Owned users plug-in
	*/

	// Actions
		$function["ownedusers:init"][] = path . "units/ownedusers/users_actions.php";
	
	// Menu button
		$function["menu:sub"][] = path . "units/ownedusers/menu_sub.php";
		
	// Get list of owned users
		$function['ownedusers:get'][] = path . "units/ownedusers/get_ownedusers.php";
		
	// Generate mgmt page		
		$function['ownedusers:owned'][] = path . "units/ownedusers/users_owner_of.php";
		$function['ownedusers:owned'][] = path . "units/ownedusers/users_create.php";
		$function['ownedusers:move'][] = path . "units/ownedusers/users_move.php";
		
	// Owned users bar down the right hand side
        $function['display:sidebar'][] = path . "units/ownedusers/users_owned.php";
		
	// Permissions for owned users
		$function['permissions:check'][] = path . "units/ownedusers/permissions_check.php";
		
	// weblogs for owned users
		$function['weblogs:ownedusers:view'][] = path . "units/ownedusers/weblogs_ownedusers_view.php";
				
		
?>