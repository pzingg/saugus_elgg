<?php
/*
 * Created on Sep 13, 2006
 *
 * LDAP Authentication Library
 * by Jim Klein
 */
 
 function authenticate_user_login($username,$password) {
 	  global $CFG;
 	  $ok = false;
	  $ds = ldap_connect($CFG->ldap_server);
	  if ($CFG->ldap_use_tls)
	  	if ($CFG->ldap_debug)
	  		ldap_start_tls($ds);
	  	else
	  		@ldap_start_tls($ds);
	  if ($CFG->ldap_debug)
	  	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, $CFG->ldap_protocol_version);
	  else
	  	@ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, $CFG->ldap_protocol_version);
	  $dn = "uid=" . $username . "," . $CFG->ldap_context;;
	  if ($CFG->ldap_debug)
	  	$r = ldap_bind($ds, $dn, $password);
	  else
	  	$r = @ldap_bind($ds, $dn, $password);
	  
	  if ($r) {
	    $ok = get_record_select('users',"username = ? AND active = ? AND user_type = ? ",
                             array($username,'yes','person'));
        if (!$ok && $CFG->ldap_auto_create) {
        	//get ldap data
        	$sr = ldap_search($ds, $CFG->ldap_context, $CFG->ldap_property."=" . $username);
	        $info = ldap_get_entries($ds, $sr);
	        $email = $info[0]["mail"][0];
	        $name = $info[0]["givenname"][0];
	        $name .= " " . $info[0]["sn"][0];
        
        	//create user
        	if (!preg_match("/^[A-Za-z0-9]{3,12}$/",$username)) 
                 $messages[] = gettext("Error! Your username must contain letters and numbers only, cannot be blank, and must be between 3 and 12 characters in length.");
            else {
	             $username = strtolower($username);
	             if (record_exists('users','username',$username)) 
	                 $messages[] = gettext("The username '$username' is already taken by another user. Please contact your system administrator.");
	             else {
					$md5password = md5($password);
					$u = new StdClass;
		            $u->name = $name;
		            $u->username = $username;
		            $u->password = $md5password;
		            $u->email = $email;
		            $u->user_type = 'person';
		            $u->owner = -1;
		            $uid = insert_record('users',$u);
		            
		            $rssresult = run("weblogs:rss:publish", array($uid, false));
		            $rssresult = run("files:rss:publish", array($uid, false));
		            $rssresult = run("profile:rss:publish", array($uid, false));
		            
		            $ok = get_record_select('users',"username = ? AND active = ? AND user_type = ? ",
                             array($username,'yes','person'));;
	             }
            }  
        }
        
	  }
      else //fall back to internal auth
      	$ok = get_record_select('users',"username = ? AND password = ? AND active = ? AND user_type = ? ",
                             array($username,md5($password),'yes','person'));
	  ldap_close($ds);
	  return $ok;
	}
	
 
?>
