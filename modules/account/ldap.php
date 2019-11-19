<?php
if(!stristr(htmlentities($_SERVER['PHP_SELF']), 'load.php'))
	exit();
if(empty($user)|| empty($pass))
	exit;
///
if($level = authenticate($user, $pass)) {
	if($level == 1)
		unset ($level);
	if($level == 2)
		$level = 'kurzy';
	if($level == 3)
		$level = 'gps kurzy admin';
	$usertoken = md5(RandomString());
	$result = sql_query("SELECT uid FROM portal.m_users WHERE username='$user' LIMIT 1");
	$dbdata = sql_fetch_array($result);
	if($dbdata['uid'])
		sql_query("UPDATE portal.m_users SET password='$usertoken', access='$level' WHERE username='$user' LIMIT 1");
	else {
		sql_query("INSERT INTO portal.m_users VALUES('','$user','$usertoken','$user" . $GLOBALS['ldap_mail'] . "','','','','',NULL,'$level','')");
		$result = sql_query("SELECT uid FROM portal.m_users WHERE username='$user' LIMIT 1");
		$dbdata = sql_fetch_array($result);
	}
	docookie($dbdata['uid'], $user, $usertoken);
	if(!stristr($_SERVER['HTTP_REFERER'], 'notice=')) {
		Header('Location: ' . $_SERVER['HTTP_REFERER']);
		exit;
	} else {
		Header('Location: /');
		exit;
	}
}

function RandomString() {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$randstring = '';
	for($i = 0;
	$i < 32;
	$i ++ ) {
		$randstring .= $characters[rand(0, strlen($characters))];
	}
	return $randstring;
}

function authenticate($user, $password) {
	if(empty($user)|| empty($password))
		return false;
	// active directory server
	$ldap_host = $GLOBALS['ldap_host'];
	// active directory DN (base location of ldap search)
	$ldap_dn = $GLOBALS['ldap_dn'];
	// active directory user group name
	$ldap_user_group = $GLOBALS['ldap_user_group'];
	// active directory manager group name
	$ldap_manager_group = $GLOBALS['ldap_manager_group'];
	// domain, for purposes of constructing $user
	$ldap_usr_dom = $GLOBALS['ldap_usr_dom'];
	// connect to active directory
	$ldap = ldap_connect($ldap_host);
	ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
	// verify user and password
	if($bind = @ ldap_bind($ldap, $user . $ldap_usr_dom, $password)) {
		// valid
		// check presence in groups
		$filter = "(sAMAccountName=" . $user . ")";
		$attr = array("memberof");
		$result = ldap_search($ldap, $ldap_dn, $filter, $attr)or exit("Unable to search LDAP server");
		$entries = ldap_get_entries($ldap, $result);
		ldap_unbind($ldap);
		// check groups

		foreach($entries[0]['memberof'] as $grps) {
			// is manager, break loop
			if(strpos($grps, $ldap_manager_group)) {
				return 3;
			}
			// is user
			if(strpos($grps, $ldap_user_group))
				return 2;
		}
		// anonymous ldap user
		return 1;
	} else {
		return false;
	}
}
