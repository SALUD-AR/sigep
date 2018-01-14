<?php

/*
 * phpSecureSite
 *
 * modules/timeout.php
 *
 * Session timeout module
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
phpss_module_register("timeout");

// register event handler
phpss_event_handler_register("session_validate_postcheck", "phpss_timeout_handler");

// int phpss_timeout_handler(str event, arr data)
// timeout handler
function phpss_timeout_handler($event, $data) {
	$cfg = phpss_module_config_get("timeout");

	$timeout = $cfg["timeout_limit"];

	// fetch data
	$sessiondata = phpss_get_session_data($data["sessionkey"]);
	$lastreq = $sessiondata["lastrequest"];
	$nowunix = date("U");

	// convert last request time
	$p = explode(" ", preg_replace("/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2}).*$/", "$1 $2 $3 $4 $5 $6", $lastreq));
	$lastrequnix = mktime($p[3], $p[4], $p[5], $p[1], $p[2], $p[0]);

	// calculate difference
	$diff = $nowunix - $lastrequnix;

	if ($diff > $timeout * 60) {
		phpss_log("timeout", "Page-request from timed-out session");
		return PHPSS_AUTH_TIMEOUT;
	}
}

?>
