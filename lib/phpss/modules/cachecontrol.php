<?php

/*
 * phpSecureSite
 *
 * modules/cachecontrol.php
 *
 * A module for controlling cache and proxy policies using
 * HTTP headers.
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
phpss_module_register("cachecontrol");

// register event handler
phpss_event_handler_register("system_init", "phpss_cachecontrol_setup");

// void phpss_cachecontrol_setup()
// sets up http headers, executed by system_init
function phpss_cachecontrol_setup() {
	$cfg = phpss_module_config_get("cachecontrol");

	// set up headers
	$headers = array();
	switch($cfg["policy"]) {
		case "nocache":
			$headers[] = "Cache-Control: no-store, no-cache" . ($cfg["revalidate"] == true ? ", must-revalidate" : "");
			$headers[] = "Pragma: no-cache";
			$headers[] = "Expires: Thu, 01 Jan 1970 00:00:01 GMT";
			break;

		case "private":
			$headers[] = "Cache-Control: private"  . ($cfg["revalidate"] == true ? ", must-revalidate" : "");
			$headers[] = "Pragma: no-cache";
			break;

		case "public":
			$headers[] = "Cache-Control: public" . ($cfg["revalidate"] == true ? ", must-revalidate" : "");
			break;
	}

	// set headers
	header(implode("\n", $headers) . "\n");
}

?>
