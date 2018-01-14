<?php

/*
 * phpSecureSite
 *
 * modules/dbbackends/mysql.php
 *
 * MySQL database backend module. Allows you to use a MySQL
 * database for data storage.
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
phpss_module_register("mysql", PHPSS_MODULE_TYPE_DBBACKEND);

// register functions
phpss_handler_register(PHPSS_HANDLER_TYPE_DB_CONNECT, "phpss_dbbackend_mysql_connect");
phpss_handler_register(PHPSS_HANDLER_TYPE_DB_QUERY, "phpss_dbbackend_mysql_query");

// res phpss_dbbackend_mysql_connect()
// connects to a database server
function phpss_dbbackend_mysql_connect() {
	$cfg = phpss_module_config_get("mysql", PHPSS_MODULE_TYPE_DBBACKEND);

	// connect to the database
	if(($link = @mysql_connect($cfg["hostname"] . ":" . $cfg["port"], $cfg["username"], $cfg["password"])) == false)
		return false;

	// select the database
	if (@mysql_select_db($cfg["database"], $link) == false)
		return false;
	
	// return the link identifier
	return $link;
}

// arr phpss_dbbackend_mysql_query(str query)
// runs a query against a database, returns the result as a matrix
function phpss_dbbackend_mysql_query($query) {
	global $phpss_db_link;

	// run the query, and check the result
	if (($rs = @mysql_query($query, $phpss_db_link)) == false)
		phpss_error("A database query failed");

	$matrix = array();
	if (strtolower(substr(trim($query), 0, 6)) == "select")
		for ($idx = 0; $idx < mysql_num_rows($rs); $idx++) // generate matrix
			$matrix[] = mysql_fetch_row($rs);

	return $matrix;
}

?>
