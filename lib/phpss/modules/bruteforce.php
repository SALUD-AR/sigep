<?php

/*
 * phpSecureSite
 *
 * modules/bruteforce.php
 *
 * Account bruteforce attack countermeasure. Will deny access to an
 * account or from an IP address after x failed login attempts.
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
phpss_module_register("bruteforce");

// register event handlers
phpss_event_handler_register("session_create_precheck", "phpss_bruteforce_precheckhandler");
phpss_event_handler_register("session_create_fail", "phpss_bruteforce_failhandler");
phpss_event_handler_register("session_create_complete", "phpss_bruteforce_successhandler");

// void phpss_bruteforce_failhandler(str event, arr data)
// handler for login failed event
function phpss_bruteforce_failhandler($event, $data) {
	$cfg = phpss_module_config_get("bruteforce");

	// register ip fail
	if ($cfg["ip"]["enable"] == true)
		phpss_bruteforce_reg_fail("ip", phpss_bruteforce_ipid($_SERVER["REMOTE_ADDR"]));

	// register account fail
	if ($cfg["account"]["enable"] == true)

		// check if the username used actually exists
		if(($accountid = phpss_get_account_id($data["username"])) == true)
			phpss_bruteforce_reg_fail("account", $accountid);
}

// void phpss_bruteforce_precheckhandler(str event, arr data)
// checks if any locks are present when authenticating users
function phpss_bruteforce_precheckhandler($event, $data) {
	$cfg = phpss_module_config_get("bruteforce");

	// check for any srcip locks
	if (	$cfg["ip"]["enable"] == true
		&& phpss_bruteforce_lock_exists("ip", phpss_bruteforce_ipid($_SERVER["REMOTE_ADDR"])) == true
	) {
		phpss_log("bruteforce", "Login denied, IP address locked by bruteforce module");
		return PHPSS_LOGIN_BRUTEFORCE_LOCK_SRCIP;
	}

	// check for any account locks
	if (	$cfg["account"]["enable"] == true
		&& ($accountid = phpss_get_account_id($data["username"])) == true
		&& phpss_bruteforce_lock_exists("account", $accountid) == true
	) {
		phpss_log("bruteforce", "Login denied, account locked by bruteforce module");
		return PHPSS_LOGIN_BRUTEFORCE_LOCK_ACCOUNT;
	}
}

// void phpss_bruteforce_successhandler(str event, arr data)
// removes any previous failed login records
function phpss_bruteforce_successhandler($event, $data) {
	$cfg = phpss_module_config_get("bruteforce");

	// remove ip fails
	if ($cfg["ip"]["enable"] == true)
		phpss_bruteforce_remove_fails("ip", phpss_bruteforce_ipid($_SERVER["REMOTE_ADDR"]));

	// remove account fails
	if ($cfg["account"]["enable"] == true)
		phpss_bruteforce_remove_fails("account", $data["accountid"]);
}

// bool phpss_bruteforce_lock_exists(str type, int id)
// checks if a lock exists
function phpss_bruteforce_lock_exists($type, $id) {
	$cfg = phpss_module_config_get("bruteforce");

	// get time of last fail (no fail found: not locked)
	if (($lastfail = phpss_bruteforce_get_last_failtime($type, $id)) == 0)
		return false;

	// check if locktime has expired
	if ($cfg[$type]["locktime"] > 0 && $lastfail > 0 && $cfg[$type]["locktime"] <= (date("U") - $lastfail))
		return false;

	// check number of fails, within thresholdtime if defined
	$query = "	SELECT
				COUNT(id)
			FROM
				phpss_bruteforce_" . $type . "
			WHERE
				" . $type . "fid = '" . $id . "'";
	if ($cfg[$type]["thresholdtime"] > 0)
		$query .= " AND time > " . ($lastfail - $cfg[$type]["thresholdtime"]);
	$res = phpss_db_query($query);

	if ($res[0][0] < $cfg[$type]["threshold"])
		return false;

	// lock exists
	return true;
}

// int phpss_bruteforce_get_last_failtime(str type, int id)
// returns the time of the last fail as a unix timestamp
function phpss_bruteforce_get_last_failtime($type, $id) {
	$query = "	SELECT
				time
			FROM
				phpss_bruteforce_" . $type . "
			WHERE
				" . $type . "fid = '" . $id . "'
			ORDER BY
				time DESC
			LIMIT 1";
	$res = phpss_db_query($query);
	return (sizeof($res) > 0 ? $res[0][0] : 0);
}

// int phpss_bruteforce_ipid(str ip)
// returns an IP address id. Registers the ip if not found
function phpss_bruteforce_ipid($ip) {
	// get ip address id
	if (($ipid = phpss_get_ip_id($ip)) == false) {
		phpss_insert_ip($ip);
		$ipid = phpss_get_ip_id($ip);
	}

	return $ipid;
}

// void phpss_bruteforce_reg_fail(str type, int id)
// registers a failed account login attempt
function phpss_bruteforce_reg_fail($type, $id) {

	// insert failed login attempt into database
	$query = "	INSERT INTO
				phpss_bruteforce_" . $type . "
			(
				" . $type . "fid,
				time
			) VALUES (
				'" . $id . "',
				'" . date("U") . "'
			)";
	phpss_db_query($query);
}

// void phpss_bruteforce_remove_fails(str type, int id)
// removes any failed login attempts
function phpss_bruteforce_remove_fails($type, $id) {
	$query = "	DELETE FROM
				phpss_bruteforce_" . $type . "
			WHERE
				" . $type . "fid = '" . $id . "'";
	phpss_db_query($query);
}

?>
