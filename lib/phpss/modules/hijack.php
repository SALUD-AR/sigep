<?php

/*
 * phpSecureSite
 *
 * modules/hijack.php
 *
 * This module is a session hijacking countermeasure. It checks the IP
 * address from which a request comes, and denies access if the IP
 * is different from the address the session was created from.
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
phpss_module_register("hijack");

phpss_event_handler_register("session_validate_postcheck", "phpss_hijack_handler");

// bool phpss_hijack_handler(str event, arr data)
// main event handler
function phpss_hijack_handler($event, $data) {
	$realip = $_SERVER["REMOTE_ADDR"];
	$ownerip = phpss_hijack_session_ip($data["sessionkey"]);

	// check if the ip has changed
	if ($realip != $ownerip) {
		phpss_log("hijack", "Possible session hijack attempt");
		return PHPSS_AUTH_HIJACK;
	}
}

// str phpss_hijack_session_ip(str sessionkey)
// returns the ip address owning a session
function phpss_hijack_session_ip($sessionkey) {
	$query = "	SELECT
				phpss_ip.ip
			FROM
				phpss_session,
				phpss_ip
			WHERE
				phpss_session.sessionkey = '" . $sessionkey . "'
				AND phpss_session.ipfid = phpss_ip.id";
	$res = phpss_db_query($query);

	return $res[0][0];
}

?>
