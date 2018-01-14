<?php

/*
 * phpSecureSite
 *
 * func/database.php
 *
 * Generic database functions
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

// res phpss_db_connect()
// connects to a database server, using the registered connect handler
function phpss_db_connect() {
	global $phpss_reg;
	return call_user_func($phpss_reg["dbbackend"]["connect"]);
}

// arr phpss_db_query(str query)
// executes a query, returning the results as a matrix
function phpss_db_query($query) {
	global $phpss_reg;
	return call_user_func($phpss_reg["dbbackend"]["query"], $query);
}

?>
