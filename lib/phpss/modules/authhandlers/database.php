<?php

/*
 * phpSecureSite
 *
 * modules/authhandlers/database.php
 *
 * Database authentication handler which will use phpSecureSite's
 * already established database link for communication
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
phpss_module_register("database", PHPSS_MODULE_TYPE_AUTHHANDLER);

// register handlers
phpss_handler_register(PHPSS_HANDLER_TYPE_AUTH_LOGIN, "phpss_authhandler_database_login");
phpss_handler_register(PHPSS_HANDLER_TYPE_AUTH_ACCOUNTDATA, "phpss_authhandler_database_get_account_data");
phpss_handler_register(PHPSS_HANDLER_TYPE_AUTH_ACCOUNTGROUPS, "phpss_authhandler_database_get_account_groups");
phpss_handler_register(PHPSS_HANDLER_TYPE_AUTH_ACCOUNTID, "phpss_authhandler_database_get_account_id");
phpss_handler_register(PHPSS_HANDLER_TYPE_AUTH_GROUPDATA, "phpss_authhandler_database_get_group_data");
phpss_handler_register(PHPSS_HANDLER_TYPE_AUTH_GROUPID, "phpss_authhandler_database_get_group_id");

// int phpss_authhandler_database_login(str username, str password)
// attempts to authenticate a user by the given identification tokens
function phpss_authhandler_database_login($username, $password) {
	$cfg = phpss_module_config_get("database", PHPSS_MODULE_TYPE_AUTHHANDLER);

	// set up type of password
	switch($cfg["pwtype"]) {
		case "md5": $sqlpw = "'" . md5($password) . "'"; break;
		case "mysqlpw": $sqlpw = "PASSWORD('" . $password . "')"; break;
		case "plaintext": $sqlpw = "'" . $password . "'"; break;
		default: phpss_error("Invalid pwtype for auth handler");
	}

	// check for the account
	$query = "	SELECT
				id
			FROM
				phpss_account
			WHERE
				LOWER(username) = '" . strtolower($username) . "'
				AND password = " . $sqlpw . "
				AND active = 'true'";
	$res = phpss_db_query($query);

	// check result, and return appropriate value
	return (sizeof($res) == 1 ? $res[0][0] : false);
}

// arr phpss_authhandler_database_get_account_data(int accountid)
// retrieves account data
function phpss_authhandler_database_get_account_data($accountid) {
	$query = "	SELECT
				id,
				username,
				password,
				active
			FROM
				phpss_account
			WHERE
				id = " . $accountid;
	$res = phpss_db_query($query);

	if (sizeof($res) == 0) {
		$data = false;
	} else {
		$data["id"] = $res[0][0];
		$data["username"] = $res[0][1];
		$data["password"] = $res[0][2];
		$data["active"] = ($res[0][3] == "true" ? true : false);
	}

	return $data;
}

// arr phpss_authhandler_database_get_account_groups(int accountid)
// retrieves the groups an account is a member of
function phpss_authhandler_database_get_account_groups($accountid) {
	$query = "	SELECT
				groupfid
			FROM
				phpss_account_group
			WHERE
				accountfid = " . $accountid;
	$res = phpss_db_query($query);

	$groups = array();
	foreach($res AS $row)
		$groups[] = $row[0];

	return $groups;
}

// int phpss_authhandler_database_get_account_id(str username)
// looks up an account id based on a username
function phpss_authhandler_database_get_account_id($username) {
	$query = "	SELECT
				id
			FROM
				phpss_account
			WHERE
				LOWER(username) = '" . strtolower($username) . "'";
	$res = phpss_db_query($query);

	return (sizeof($res) > 0 ? $res[0][0] : false);
}

// arr phpss_authhandler_database_get_group_data(int groupid)
// retrieves group data
function phpss_authhandler_database_get_group_data($groupid) {
	$query = "	SELECT
				id,
				name
			FROM
				phpss_group
			WHERE
				id = " . $groupid;
	$res = phpss_db_query($query);

	if (sizeof($res) == 0) {
		$data = false;
	} else {
		$data["id"] = $res[0][0];
		$data["name"] = $res[0][1];
	}

	return $data;
}

// int phpss_authhandler_database_get_group_id(str groupname)
// looks up the id of a group from its name
function phpss_authhandler_database_get_group_id($groupname) {
	$query = "	SELECT
				id
			FROM
				phpss_group
			WHERE
				name = '" . $groupname . "'";
	$res = phpss_db_query($query);

	return (sizeof($res) > 0 ? $res[0][0] : false);
}

?>
