<?php

/*
 * phpSecureSite
 *
 * func/event.php
 *
 * Event-related functions
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

// void phpss_event(str event[, arr data])
// emits an event ($data array will include various environment data for the handlers)
function phpss_event($event, $data = array()) {
	global $phpss_reg;

	// check if the event exists
	if (phpss_event_exists($event) == false)
		phpss_error("Event '" . $event . "' not found when attempting to emit it");

	// run event handlers
	// if an event handler returns a value other than ""
	// the event will be aborted, and the value returned to the caller
	foreach($phpss_reg["events"][$event] AS $handler)
		if (($status = call_user_func($handler, $event, $data)) != "")
			return $status;
}

// bool phpss_event_exists(str event)
// check if an event exists
function phpss_event_exists($event) {
	global $phpss_reg;
	return isset($phpss_reg["events"][$event]);
}

// void phpss_event_handler_register(str event, str handler)
// registers an event handler
function phpss_event_handler_register($event, $handler) {
	global $phpss_reg;

	// check if the event exists
	if (phpss_event_exists($event) == false)
		phpss_error("Event '" . $event . "' not found when registering handler '" . $handler . "'");

	// check if the handler function exists
	if (function_exists($handler) == false)
		phpss_error("Function '" . $handler . "' non-existant when registering handler for event '" . $event . "'");

	// register the handler
	$phpss_reg["events"][$event][] = $handler;
}

// bool phpss_event_register(str event)
// registers an event
function phpss_event_register($event) {
	global $phpss_reg;

	// check if the event exists
	if (phpss_event_exists($event) == true)
		phpss_error("The event '" . $event . "' is already registered");

	// register the event
	$phpss_reg["events"][$event] = array();
}

?>
