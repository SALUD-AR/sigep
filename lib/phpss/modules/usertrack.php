<?php

/*
 * phpSecureSite
 *
 * modules/usertrack.php
 *
 * Module which records what pages are requested by a user.
 * Documented in the main phpSecureSite documentation.
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
phpss_module_register("usertrack");

// register event handler
phpss_event_handler_register("session_validate_success", "phpss_usertrack_handler");

// void phpss_usertrack_handler(str event, arr data)
// does all the work ;)
function phpss_usertrack_handler($event, $data) {

	// check for the session key
	if (isset($data["sessionkey"]) == false)
		phpss_error("Sessionkey not passed to usertrack module");

	$sessionkey = $data["sessionkey"];

	// fetch request data
	$sessiondata = phpss_get_session_data($sessionkey);
	$sessionid = $sessiondata["id"];
	$accountid = $sessiondata["accountid"];
	$urlid = phpss_usertrack_url($_SERVER["SCRIPT_NAME"]);

	if ($urlid == 0)
		phpss_error("usertrack module unable to register url");

	// register the request
	phpss_usertrack_register($urlid, $sessionid, $accountid);
}

// void phpss_usertrack_register(int urlid, int sessionid, int accountid)
// registers a page request
function phpss_usertrack_register($urlid, $sessionid, $accountid) {
	$query = "	INSERT INTO
				phpss_usertrack_req
			(
				sessionfid,
				accountfid,
				timestamp,
				usertrack_urlfid
			) VALUES (
				'" . $sessionid . "',
				'" . $accountid . "',
				'" . date("Y-m-d H:i:s") . "',
				" . $urlid . "
			)";
	phpss_db_query($query);
}

// int phpss_usertrack_url(str url)
// returns the id of an url, registering it if not already in db
function phpss_usertrack_url($url) {
	$urlid = phpss_usertrack_url_get_id($url);

	if ($urlid == 0) {
		phpss_usertrack_url_reg($url);
		$urlid = phpss_usertrack_url_get_id($url);
	}

	return $urlid;
}

// int phpss_usertrack_url_get_id(str url)
// returns the id of an url, or 0 if not found
function phpss_usertrack_url_get_id($url) {
	$query = "	SELECT
				id
			FROM
				phpss_usertrack_url
			WHERE
				url = '" . $url . "'";

	$res = phpss_db_query($query);

	return (sizeof($res) > 0 ? $res[0][0] : 0);
}

// void phpss_usertrack_url_reg(str url)
// registers an url in the database
function phpss_usertrack_url_reg($url) {
	phpss_db_query("INSERT INTO phpss_usertrack_url ( url ) VALUES ('" . $url . "')");
}

?>
