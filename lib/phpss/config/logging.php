<?php

/*
 * phpSecureSite
 *
 * config.php
 *
 * Configuration file for log backend modules. Detailed explanations
 * of config options and modules are available in the documentation.
 * Log backend modules takes care of storing log messages.
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

// Enable logging?
$phpss_cfg["phpss"]["logging"]		= false;

// Name of the log backend module to use
$phpss_cfg["phpss"]["logbackend"]	= "database";




/*
 * csv logbackend - logs to comma-separated textfiles
 *
 * Log entries are stored in a plain-text file, with several fields separated
 * by a character.
 *
*/

$phpss_cfg["logbackend"]["csv"] = array(
	"modulefile"		=> PHPSS_ROOT_FS . "/modules/logbackends/csv.php",

	// The file to write to (make sure the file permissions are ok)
	"logfile"		=> "/var/log/phpss",

	// Character to put between fields. Should be something that is
	// unlikely to appear in the log entries.
	"fieldsep"		=> ";",

	// Character to use for separating lines. UNIX systems uses the
	// special character "\n", while Windows uses "\r\n".
	"linesep"		=> "\n"
);




/*
 * database logbackend - logs to the phpsecuresite database
 *
 * Log entries are stored in the phpss_log table, which is part of the
 * phpSecureSite database.
 *
*/

$phpss_cfg["logbackend"]["database"] = array(
	"modulefile"		=> PHPSS_ROOT_FS . "/modules/logbackends/database.php"

	// no config options, it just works
);




/*
 * syslog logbackend - logs to the unix syslog
 *
 * Log entries are sent to the UNIX syslog system. On Windows, this might
 * (according to the PHP website) log to the event logger, but this has never
 * been tested. 
 *
*/

$phpss_cfg["logbackend"]["syslog"] = array(
	"modulefile"		=> PHPSS_ROOT_FS . "/modules/logbackends/syslog.php",

	// Log facility to use (see syslog(3) man page for info)
	"facility"		=> LOG_AUTHPRIV
);

?>
