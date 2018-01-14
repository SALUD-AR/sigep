<?php

/*
 * phpSecureSite
 *
 * modules/logbackends/syslog.php
 *
 * Log backend for logging to the UNIX syslog (or the event log on
 * windows). Log priorities are currently not implemented, LOG_INFO
 * is always used.
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
phpss_module_register("syslog", PHPSS_MODULE_TYPE_LOGBACKEND);

// register handler
phpss_handler_register(PHPSS_HANDLER_TYPE_LOG_MESSAGE, "phpss_logbackend_syslog_handler");

// void phpss_logbackend_syslog_handler(str module, str time, str sessionkey, str ip, str message)
// sends a log message to the syslog
function phpss_logbackend_syslog_handler($module, $time, $sessionkey, $ip, $message) {
	$cfg = phpss_module_config_get("syslog", PHPSS_MODULE_TYPE_LOGBACKEND);

	// initialize vars
	$text_ip = $text_session = $text_user = "";

	// open log
	if (openlog("phpss", (LOG_NDELAY | LOG_PID), $cfg["facility"]) == false)
		phpss_error("Unable to open syslog log system");

	// fetch info
	$text_ip = "from " . $ip;
	
	if ($sessionkey == true) {
		$sessiondata = phpss_get_session_data($sessionkey);
		$text_session = "using sessionkey '" . $sessionkey . "'";

		if ($sessiondata["accountid"] == true) {
			$ownerdata = phpss_get_account_data($sessiondata["accountid"]);
			$text_user = "user " . $ownerdata["id"] . " (" . $ownerdata["username"] . ")";
		} else {
			$text_user = "anonymous user";
		}
	}

	$clienttext = $text_ip . ($text_user != "" ? ", " . $text_user : "") . ($text_session != "" ? ", " . $text_session : "");

	// write log line
	$line = "(module " . $module . ") " . $message . " [" . $clienttext . "]";
	syslog(LOG_INFO, $line);

	// close log
	closelog();
}

?>
