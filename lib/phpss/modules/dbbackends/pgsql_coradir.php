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
phpss_module_register("pgsql_coradir", PHPSS_MODULE_TYPE_DBBACKEND);

// register functions
phpss_handler_register(PHPSS_HANDLER_TYPE_DB_CONNECT, "phpss_dbbackend_pgsql_coradir_connect");
phpss_handler_register(PHPSS_HANDLER_TYPE_DB_QUERY, "phpss_dbbackend_pgsql_coradir_query");

// res phpss_dbbackend_pgsql_connect(str hostname, int port, str username, str password, str database)
// connects to a database server
function phpss_dbbackend_pgsql_coradir_connect() {
	global $db;
	if(!$db)
		return false;
	// return the link identifier
	return $db;
}

// arr phpss_dbbackend_pgsql_query(str query)
// runs a query against a database, returns the result as a matrix
function phpss_dbbackend_pgsql_coradir_query($query) {
	// run the query
	db_tipo_res("d");
	$rs = sql($query,"PHPSS") or die;

	$matrix = array();
	while (!$rs->EOF) {
		$matrix[] = $rs->fields;
		$rs->MoveNext();
	}
	return $matrix;
}

?>
