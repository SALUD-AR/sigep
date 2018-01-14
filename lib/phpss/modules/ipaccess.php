<?php

/*
 * phpSecureSite
 *
 * modules/ipaccess.php
 *
 * Allow or deny access based on the clients IP address. See docs for
 * more information.
 *
 *
 * Copyright (C) 2002-2003 Erik Grinaker
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
*/

// register module
phpss_module_register("ipaccess");

// set up event handlers
phpss_event_handler_register("session_create_authhandler", "phpss_ipaccess_loginhandler");
phpss_event_handler_register("session_validate_postcheck", "phpss_ipaccess_sessionhandler");

// set up constants
define("PHPSS_IPACCESS_ALLOW", 2);
define("PHPSS_IPACCESS_DENY", 1);
define("PHPSS_IPACCESS_UNKNOWN", "");

// bool phpss_ipaccess_loginhandler(str event, arr data)
// handles checks at login-time
function phpss_ipaccess_loginhandler($event, $data) {

	$accountid = $data["accountid"];

	if ($accountid != false && phpss_ipaccess_check($accountid, $_SERVER["REMOTE_ADDR"]) == false) {
		phpss_log("ipaccess", "Logins not allowed from this IP address");
		return PHPSS_LOGIN_IPACCESS_DENY;
	}
}

// bool phpss_ipaccess_sessionhandler(str event, arr data)
// handles checks at session validation time
function phpss_ipaccess_sessionhandler($event, $data) {
	$cfg = phpss_module_config_get("ipaccess");

	// fetch account id
	$sessiondata = phpss_get_session_data($data["sessionkey"]);
	$accountid = $sessiondata["accountid"];

	if ($cfg["checktime"] == "always")
		if (phpss_ipaccess_check($accountid, $_SERVER["REMOTE_ADDR"]) == false) {
			phpss_log("ipaccess", "Page requests not allowed from this IP address");
			return PHPSS_AUTH_IPACCESS_DENY;
		}
}

// bool phpss_ipaccess_check(int accountid, str ip)
// checks if a client is allowed access
function phpss_ipaccess_check($accountid, $ip) {
	$cfg = phpss_module_config_get("ipaccess");

	// check account access
	$access = phpss_ipaccess_get_account_access($accountid, $ip);

	// check group access
	if ($access == PHPSS_IPACCESS_UNKNOWN)
		$access = phpss_ipaccess_get_group_access($accountid, $ip);

	// check global access
	if ($access == PHPSS_IPACCESS_UNKNOWN)
		$access = phpss_ipaccess_get_global_access($ip);

	// if no access found yet, use policy
	if ($access == PHPSS_IPACCESS_UNKNOWN)
		$access = $cfg["policy"] == "allow" ? PHPSS_IPACCESS_ALLOW : PHPSS_IPACCESS_DENY;

	return ($access == PHPSS_IPACCESS_ALLOW ? true : false);
}

// int phpss_ipaccess_get_account_access(int accountid, str ip)
// checks if an account is allowed access
function phpss_ipaccess_get_account_access($accountid, $ip) {
	$query = "SELECT access FROM phpss_ipaccess_account WHERE accountfid = '" . $accountid . "' AND ip = '" . $ip . "'";
	$rs = phpss_db_query($query);

	// check access
	if (sizeof($rs) > 0) {
		$status = $rs[0][0] == "allow" ? PHPSS_IPACCESS_ALLOW : PHPSS_IPACCESS_DENY;
	} else {
		$status = PHPSS_IPACCESS_UNKNOWN;
	}

	return $status;	
}

// int phpss_ipaccess_get_global_access(str ip)
// checks if an ip address is allowed access
function phpss_ipaccess_get_global_access($ip) {
	$query = "SELECT access FROM phpss_ipaccess WHERE ip = '" . $ip . "'";
	$rs = phpss_db_query($query);

	// check access
	if (sizeof($rs) > 0) {
		$status = $rs[0][0] == "allow" ? PHPSS_IPACCESS_ALLOW : PHPSS_IPACCESS_DENY;
	} else {
		$status = PHPSS_IPACCESS_UNKNOWN;
	}

	return $status;
}

// int phpss_ipaccess_get_group_access(int accountid, str ip)
// checks if group(s) are allowed access
function phpss_ipaccess_get_group_access($accountid, $ip) {
	$cfg = phpss_module_config_get("ipaccess");

	// get account groups
	$groups = phpss_get_account_groups($accountid);

	// get access rules for each group
	$accesses = array();
	foreach($groups AS $group) {
		$rs = phpss_db_query("SELECT access FROM phpss_ipaccess_group WHERE groupfid = '" . $group . "' AND ip = '" . $ip . "'");
	
		if (sizeof($rs) > 0)
			$accesses[] = ($rs[0][0] == "allow" ? PHPSS_IPACCESS_ALLOW : PHPSS_IPACCESS_DENY);
	}

	// get unique access rules
	$uniqueaccesses = array_values(array_unique($accesses));

	// check if any access rules were found
	if (sizeof($uniqueaccesses) == 0) {
		$access = PHPSS_IPACCESS_UNKNOWN;

	// check if only one access type was found
	} elseif (sizeof($uniqueaccesses) == 1) {
		$access = $uniqueaccesses[0];

	// check for several conflicting access types
	} else {
		$access = ($cfg["preferred"] == "allow" ? PHPSS_IPACCESS_ALLOW : PHPSS_IPACCESS_DENY);
	}

	// return access
	return $access;
}

?>
