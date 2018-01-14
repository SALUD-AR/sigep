<?php

/*
 * phpSecureSite
 *
 * modules/dbbackends/pgsql.php
 *
 * PostgreSQL database backend module. Allows you to use a PostgreSQL
 * database server for data storage.
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
phpss_module_register("pgsql", PHPSS_MODULE_TYPE_DBBACKEND);

// register functions
phpss_handler_register(PHPSS_HANDLER_TYPE_DB_CONNECT, "phpss_dbbackend_pgsql_connect");
phpss_handler_register(PHPSS_HANDLER_TYPE_DB_QUERY, "phpss_dbbackend_pgsql_query");

// res phpss_dbbackend_pgsql_connect(str hostname, int port, str username, str password, str database)
// connects to a database server
function phpss_dbbackend_pgsql_connect() {
	$cfg = phpss_module_config_get("pgsql", PHPSS_MODULE_TYPE_DBBACKEND);

	// connect to the database
	$connstring = "host=" . $cfg["hostname"] . " port=" . $cfg["port"] . " dbname=" . $cfg["database"] . " user=" . $cfg["username"] . " password=" . $cfg["password"];
	if (($link = @pg_connect($connstring)) == false)
		return false;

	// return the link identifier
	return $link;
}

// arr phpss_dbbackend_pgsql_query(str query)
// runs a query against a database, returns the result as a matrix
function phpss_dbbackend_pgsql_query($query) {
	global $phpss_db_link;

	// run the query
	if (($rs = @pg_query($phpss_db_link, $query)) == false)
		phpss_error("A database query failed to execute");

	$matrix = array();
	for ($idx = 0; $idx < pg_num_rows($rs); $idx++)
		$matrix[] = pg_fetch_row($rs, $idx);

	return $matrix;
}

?>
