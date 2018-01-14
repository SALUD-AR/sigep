<?php

/*
 * phpSecureSite
 *
 * modules/logbackends/database.php
 *
 * Log backend for logging to a database, uses the phpSecureSite database
 * and database connection
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
phpss_module_register("database", PHPSS_MODULE_TYPE_LOGBACKEND);

// register handler
phpss_handler_register(PHPSS_HANDLER_TYPE_LOG_MESSAGE, "phpss_logbackend_db_handler");

// void phpss_logbackend_db_handler(str module, str time, str sessionkey, str ip, str message)
// writes log data to a database table
function phpss_logbackend_db_handler($module, $time, $sessionkey, $ip, $message) {

	// fetch session data
	$sessiondata = phpss_get_session_data($sessionkey);
	if ($sessiondata["id"] == "") {
		$sessionid = "NULL";
		$accountid = "NULL";
	} else {
		$sessionid = "'" . $sessiondata["id"] . "'";
		$accountid = "'" . $sessiondata["accountid"] . "'";
	}

	// get ip database id
	if (($ipid = phpss_get_ip_id($ip)) == false) {
		phpss_insert_ip($ip);
		$ipid = phpss_get_ip_id($ip);
	}

	// create the log entry
	$query = "	INSERT INTO
				phpss_log
			(
				timestamp,
				sessionfid,
				accountfid,
				ipfid,
				module,
				message
			) VALUES (
				'" . $time . "',
				" . $sessionid. ",
				" . $accountid . ",
				'" . $ipid . "',
				'" . $module . "',
				'" . addslashes($message) . "'
			)";
	phpss_db_query($query);
}

?>
