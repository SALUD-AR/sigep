<?php

/*
 * phpSecureSite
 *
 * modules/acl.php
 *
 * A module which provides access control lists.
 * Documented in the main phpSecureSite documentation.
 *
 * No validation on fetched data, as it should be set using
 * the interface functions, and these perform data validation.
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
phpss_module_register("acl");

// set constants
define("PHPSS_ACL_UNKNOWN", "");
define("PHPSS_ACL_DENY", "deny");
define("PHPSS_ACL_ALLOW", "allow");

// register event handler
phpss_module_init_register("acl", "phpss_acl_init");
phpss_event_handler_register("session_validate_postcheck", "phpss_acl_handler");

// void phpss_acl_init()
// sets defaults based on config options
function phpss_acl_init() {
	global $phpss_reg;

	// set defaults based on config options
	$cfg = phpss_module_config_get("acl");
	phpss_acl_set_policy($cfg["policy"]);
	phpss_acl_set_preferred($cfg["preferred"]);

	// set up data structure
	$phpss_reg["modules"]["acl"]["group"] = array();
	$phpss_reg["modules"]["acl"]["account"] = array();
}

// int phpss_acl_handler(str event, arr data)
// the main handler function
function phpss_acl_handler($event, $data) {

	// fetch account id
	$sessiondata = phpss_get_session_data($data["sessionkey"]);
	$accountid = $sessiondata["accountid"];

	// check for account access
	$access = phpss_acl_get_account_access($accountid);

	// check for group access if account access unknown
	if ($access == PHPSS_ACL_UNKNOWN)
		$access = phpss_acl_get_group_access_multiple(phpss_get_account_groups($accountid));

	// if no access rules are found use access policy
	if ($access == PHPSS_ACL_UNKNOWN)
		$access = phpss_acl_get_policy();

	// if access is denied, return error code and log the attempt
	if ($access == PHPSS_ACL_DENY) {
		phpss_log("acl", "Permission denied by ACL (page " . $_SERVER["SCRIPT_NAME"] . ")");
		return PHPSS_AUTH_ACLDENY;
	}

}

// int phpss_acl_get_account_access(int accountid)
// returns the access status for an account, or nothing if no access is set
function phpss_acl_get_account_access($accountid) {
	global $phpss_reg;
	$access = isset($phpss_reg["modules"]["acl"]["account"][$accountid]) ? $phpss_reg["modules"]["acl"]["account"][$accountid] : "";
	return (phpss_acl_valid_access($access, true) ? $access : PHPSS_ACL_DENY);
}

// int phpss_acl_get_policy()
// returns the current access policy
function phpss_acl_get_policy() {
	global $phpss_reg;
	$access = $phpss_reg["modules"]["acl"]["policy"];
	return (phpss_acl_valid_access($access) ? $access : PHPSS_ACL_DENY);
}

// int phpss_acl_get_group_access(int groupid)
// returns the access status for a group, or nothing is not set
function phpss_acl_get_group_access($groupid) {
	global $phpss_reg;
	$access = isset($phpss_reg["modules"]["acl"]["group"][$groupid]) ? $phpss_reg["modules"]["acl"]["group"][$groupid] : "";
	return (phpss_acl_valid_access($access, true) ? $access : PHPSS_ACL_DENY);
}

// int phpss_acl_get_group_access_multiple(arr groups)
// returns an access for a set of groups (applies preferred access if conflicting)
function phpss_acl_get_group_access_multiple($groups) {

	// retrieve all group access rules
	$groupaccess = array();
	foreach($groups AS $groupid)
		if (($curgroupaccess = phpss_acl_get_group_access($groupid)) != PHPSS_ACL_UNKNOWN)
			$groupaccess[] = $curgroupaccess;

	// if any access rules are found, determine access
	if (sizeof($groupaccess) > 0) {

		// get group access
		$access = (sizeof(array_unique($groupaccess)) > 1 ? phpss_acl_get_preferred() : $groupaccess[0]);
	} else {
		$access = PHPSS_ACL_UNKNOWN;
	}

	return $access;
}

// int phpss_acl_get_preferred()
// returns the preferred access (for groups with conflicting access rules)
function phpss_acl_get_preferred() {
	global $phpss_reg;
	$access = $phpss_reg["modules"]["acl"]["preferred"];
	return (phpss_acl_valid_access($access) ? $access : PHPSS_ACL_DENY);
}

// void phpss_acl_set_account_access(int accountid, str access)
// registers an account access status
function phpss_acl_set_account_access($accountid, $access) {
	global $phpss_reg;
	if (phpss_acl_valid_access($access) == false) phpss_error("Invalid acl access definition");
	$phpss_reg["modules"]["acl"]["account"][$accountid] = $access;
}

// void phpss_acl_set_group_access(int groupid, str access)
// registers a group access status
function phpss_acl_set_group_access($groupid, $access) {
	global $phpss_reg;
	if (phpss_acl_valid_access($access) == false) phpss_error("Invalid acl access definition");
	$phpss_reg["modules"]["acl"]["group"][$groupid] = $access;
}

// void phpss_acl_set_policy(str access)
// sets the acl policy for the page to either allow or deny
function phpss_acl_set_policy($access) {
	global $phpss_reg;
	if (phpss_acl_valid_access($access) == false) phpss_error("Invalid acl access definition '" . $access . "'");
	$phpss_reg["modules"]["acl"]["policy"] = $access;
}

// void phpss_acl_set_preferred(str access)
// sets the preferred access for users who are explicitly allowed *and* denied
// (several groups)
function phpss_acl_set_preferred($access) {
	global $phpss_reg;
	if (phpss_acl_valid_access($access) == false) phpss_error("Invalid access definition");
	$phpss_reg["modules"]["acl"]["preferred"] = $access;
}

// bool phpss_acl_valid_access(str access[, bool unknownvalid])
// checks if an access definition is valid
function phpss_acl_valid_access($access, $unknownvalid = false) {
	return ($access == PHPSS_ACL_ALLOW || $access == PHPSS_ACL_DENY || ($unknownvalid == true &&$access == PHPSS_ACL_UNKNOWN) ? true : false);
}

?>
