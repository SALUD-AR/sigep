<?php

/*
 * phpSecureSite
 *
 * config/database.php
 *
 * Configuration file for database backend modules. Detailed explanations
 * of config options and modules are available in the documentation.
 * Database backend modules are the modules which takes care of communicating
 * with the database server that holds the phpSecureSite data.
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


// Name of the database backend module to use. This should be one of the
// modules defined below in this file. You only need to configure the
// database backend module you put here.
$phpss_cfg["phpss"]["dbbackend"] = "pgsql_coradir";



/*
 * mysql database backend module - for use with MySQL database servers
 *
 * This module handles basic database communication against a MySQL database
 * server
 *
*/

$phpss_cfg["dbbackend"]["mysql"] = array(
	"modulefile"		=> PHPSS_ROOT_FS . "/modules/dbbackends/mysql.php",

	// database server address (fully-qualified domain name or ip address)
	"hostname"		=> "localhost",

	// tcp port number to connect to (mysql uses 3306 by default)
	"port"			=> 3306,

	// username and password to connect with
	"username"		=> "notroot",
	"password"		=> "secret",

	// the database to use
	"database"		=> "phpss"
);




/*
 * pgsql database backend module - for use with PostgreSQL database servers
 *
 * This module handles basic database communication against a PostgreSQL
 * database server
 *
*/

$phpss_cfg["dbbackend"]["pgsql"] = array(
	"modulefile"		=> PHPSS_ROOT_FS . "/modules/dbbackends/pgsql.php",

	// database server address (fully-qualified domain name or ip address)
	"hostname"		=> "localhost",

	// tcp port number to connect to (postgresql uses 5432 by default)
	"port"			=> 5432,

	// username and password to connect with
	"username"		=> "projekt",
	"password"		=> "propcp",

	// the database to use
	"database"		=> "gestion"
);

$phpss_cfg["dbbackend"]["pgsql_coradir"] = array(
	"modulefile"		=> PHPSS_ROOT_FS . "/modules/dbbackends/pgsql_coradir.php",
);


?>
