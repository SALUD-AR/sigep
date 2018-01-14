<?php

/*
 * phpSecureSite
 *
 * modules/logbackends/csv.php
 *
 * Log backend for csv (comma separated values) files
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

// register log backend
phpss_module_register("csv", PHPSS_MODULE_TYPE_LOGBACKEND);

// settings
phpss_module_config_store("csv", array(
	"logfile"	=> "/var/log/phpss.log",	// log file to write to
	"fieldsep"	=> ";",				// field separator
	"linesep"	=> "\n",			// line separator
), PHPSS_MODULE_TYPE_LOGBACKEND);

// register log handler
phpss_handler_register(PHPSS_HANDLER_TYPE_LOG_MESSAGE, "phpss_log_backend_csv_handler");

// void phpss_log_backend_csv_handler()
// Appends the log line entry to the csv file
function phpss_log_backend_csv_handler($module, $time, $sessionkey, $ip, $message) {
	$cfg = phpss_module_config_get("csv", PHPSS_MODULE_TYPE_LOGBACKEND);

	// get session data
	$sessiondata = phpss_get_session_data($sessionkey);
	if ($sessiondata["id"] == "") {
		$sessionid = 0;
		$accountid = 0;
	} else {
		$sessionid = $sessiondata["id"];
		$accountid = $sessiondata["accountid"];
	}

	// generate line
	$line = implode($cfg["fieldsep"], array($module, $time, $sessionid, $sessionkey, $accountid, $ip, $message)) . $cfg["linesep"];

	// write line to file
	if (($fp = @fopen($cfg["logfile"], "a+")) == false || @fwrite($fp, $line) == -1 )
		phpss_error("Unable to write log entry to log file");

	@fclose($fp);
}

?>
