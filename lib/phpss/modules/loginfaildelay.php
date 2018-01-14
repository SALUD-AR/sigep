<?php

/*
 * phpSecureSite
 *
 * modules/loginfaildelay.php
 *
 * This module attempts to slow down brute force account attacks and
 * password guessing by creating a delay before the login denied message
 * is displayed.
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
phpss_module_register("loginfaildelay");

// register event handler
phpss_event_handler_register("session_create_fail", "phpss_loginfaildelay_delay");

// void phpss_loginfaildelay_delay(str event, arr data)
// delays "login failed" messages
function phpss_loginfaildelay_delay($event, $data) {
	$cfg = phpss_module_config_get("loginfaildelay");
	sleep($cfg["delay"]);
}

?>
