<?php

/*
 * phpSecureSite
 *
 * modules/svars.php
 *
 * Provides session variables functionality
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
phpss_module_register("svars");

// register handler
phpss_event_handler_register("session_validate_success", "phpss_svars_autoget_handler");

// void phpss_svars_autoget_handler(str event, arr data)
// fetches all session variables, and sets them in the global scope
function phpss_svars_autoget_handler($event, $data) {
	$cfg = phpss_module_config_get("svars");

	if ($cfg["autoset"] == true) {
		$sessionid = phpss_svars_get_session_id($data["sessionkey"]);

		// fetch session variables
		$query = "	SELECT
					name,
					data
				FROM
					phpss_svars
				WHERE
					sessionfid = '" . $sessionid . "'";
		$res = phpss_db_query($query);

		// set all variables in the global scope
		foreach($res AS $row) {
            global ${$row[0]};
            ${$row[0]} = unserialize(base64_decode($row[1]));
		}
	}
}

// mixed phpss_svars_get(str name)
// fetches a session variable from the database
function phpss_svars_get($name) {

	$sessionid = phpss_svars_get_session_id();

	// fetch session variable
	$query = "	SELECT
				data
			FROM
				phpss_svars
			WHERE
				sessionfid = '" . $sessionid . "'
				AND name = '" . $name . "'";
	$res = phpss_db_query($query);

	return (sizeof($res) > 0 ? unserialize(base64_decode($res[0][0])) : "");
}

// void phpss_svars_set(str name, mixed data)
// stores a session variable in the database
function phpss_svars_set($name, $data) {
	global $$name;
	$$name = $data;
	// fetch session data
	$sessionid = phpss_svars_get_session_id();

	// serialize data, and escape special chars
	$sqldata = base64_encode(serialize($data));

	// check whether to update or insert the entry
	if (phpss_svars_exists($name, $sessionid) == true) {
		// update existing session variable
		$query = "	UPDATE
					phpss_svars
				SET
					data = '" . $sqldata . "'
				WHERE
					sessionfid = '" . $sessionid . "'
					AND name = '" . $name . "'";
	} else {
		// insert a new entry
		$query = "	INSERT INTO
					phpss_svars
				(
					sessionfid,
					name,
					data
				) VALUES (
					'" . $sessionid . "',
					'" . $name . "',
					'" . $sqldata . "'
				)";
	}
	phpss_db_query($query);
}

// bool phpss_svars_exists(str name[, int sessionid])
// checks if a session variable exists, if sessionid is not
// specified a query will be run to determine it
function phpss_svars_exists($name, $sessionid = "") {
	if ($sessionid == "")
		$sessionid = phpss_svars_get_session_id();

	// fetch session vars matching name and sessionid
	$query = "	SELECT
				COUNT(id)
			FROM
				phpss_svars
			WHERE
				name = '" . $name . "'
				AND sessionfid = '" . $sessionid . "'";
	$res = phpss_db_query($query);

	// check if session var exists
	return ($res[0][0] > 0 ? true : false);
}

// int phpss_svars_get_session_id([str sessionkey])
// fetches a session id
function phpss_svars_get_session_id($sessionkey = "") {
	$phpsscfg = phpss_module_config_get("phpss");

	// fetch session key
	if ($sessionkey == "")
		$sessionkey = $_COOKIE[$phpsscfg["cookie"]["name"]];

	// fetch session id
	$sessiondata = phpss_get_session_data($sessionkey);
	return $sessiondata["id"];
}

?>
