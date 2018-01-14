<?php

/*
 * phpSecureSite
 *
 * func/session.php
 *
 * Functions related to session generation / validation
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


// arr phpss_get_session_data(str sessionkey)
// returns an array of data about the session, or false if the session doesn't exist
function phpss_get_session_data($sessionkey) {
	$query = "	SELECT
				id,
				sessionkey,
				accountfid,
				ipfid,
				created,
				lastrequest,
				active
			FROM
				phpss_session
			WHERE
				sessionkey = '" . $sessionkey . "'";
	$rs = phpss_db_query($query);

	if (sizeof($rs) > 0) {
		$data["id"] = $rs[0][0];
		$data["key"] = $rs[0][1];
		$data["accountid"] = $rs[0][2];
		$data["ipid"] = $rs[0][3];
		$data["created"] = $rs[0][4];
		$data["lastrequest"] = $rs[0][5];
		$data["active"] = $rs[0][6];
	} else {
		$data = false;
	}

	return $data;
}

// void phpss_session_create(str sessionkey, int accountid)
// creates a session entry in the database
function phpss_session_create($sessionkey, $accountid) {

	// get ip address of client, and insert into database
	$ip = $_SERVER['REMOTE_ADDR'];
	if (($ipid = phpss_get_ip_id($ip)) == false) {
		phpss_insert_ip($ip);
		$ipid = phpss_get_ip_id($ip);
	}

	// format account id
	$accountid = (is_numeric($accountid) && $accountid > 0 ? "'" . $accountid . "'" : "NULL");

	// create session entry
	$query = "	INSERT INTO
				phpss_session
			(
				sessionkey,
				accountfid,
				ipfid,
				created,
				lastrequest,
				active
			) VALUES (
				'" . $sessionkey . "',
				" . $accountid . ",
				'" . $ipid . "',
				'" . date("Y-m-d H:i:s") . "',
				'" . date("Y-m-d H:i:s") . "',
				'true'
			)";
	phpss_db_query($query);
}

// str phpss_session_genkey()
// generates a unique, unused session key
function phpss_session_genkey() {
	
	// run this loop until an unused session key is found
	do {
		$key = md5(uniqid(rand()));	// generate a 32-byte session key
		$query = "	SELECT
					COUNT(id)
				FROM
					phpss_session
				WHERE
					sessionkey = '" . $key . "'";
		$res = phpss_db_query($query);
	} while($res[0][0] > 0);

	return $key;
}

// str phpss_session_setup(int accountid)
// creates a session for the specified account, and sets cookies etc
function phpss_session_setup($accountid) {
	global $phpss_session_key;
	$cfg = phpss_module_config_get("phpss");
	
	// generate session key
	$sessionkey = phpss_session_genkey();

	// create the session entry
	phpss_session_create($sessionkey, $accountid);

	// set the cookie
	$c = $cfg["cookie"]; // ok, so I'm lazy ;)
	setcookie($c["name"], $sessionkey, $c["expire"], $c["path"], $c["domain"], $c["secure"]);

	// set $phpss_session_key so that the user is logged in on this page too
	// (cookies are only valid from the next page request)
	$phpss_session_key = $sessionkey;

	return $sessionkey;
}

// void phpss_set_session_lastrequest(str sessionkey, str lastrequest)
// updates a session's last request time
function phpss_set_session_lastrequest($sessionkey, $lastrequest) {
	$query = "	UPDATE
				phpss_session
			SET
				lastrequest = '" . date("Y-m-d H:i:s") . "'
			WHERE
				sessionkey = '" . $sessionkey . "'";
	phpss_db_query($query);
}

// void phpss_set_session_status(str sessionkey, bool active)
// enables/disables a session
function phpss_set_session_status($sessionkey, $active) {
	$status = ($active == true ? "true" : "false");

	// update database record status
	$query = "	UPDATE
				phpss_session
			SET
				active = '" . $status . "'
			WHERE
				sessionkey = '" . $sessionkey . "'";
	phpss_db_query($query);
}

?>
