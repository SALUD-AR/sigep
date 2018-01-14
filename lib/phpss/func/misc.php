<?php

/*
 * phpSecureSite
 *
 * func/misc.php
 *
 * Miscellaneous functions
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

// void phpss_error(str errmsg)
// Prints an error message and exits
function phpss_error($errmsg) {
	exit("<b>phpss error :</b> " . $errmsg);
}

// int phpss_get_ip_id(str ip)
// fetches the id of an ip-address
function phpss_get_ip_id($ip) {
	$rs = phpss_db_query("SELECT id AS ipid FROM phpss_ip WHERE ip = '" . $ip . "'");
	return (sizeof($rs) > 0 ? $rs[0][0] : false);
}

// bool phpss_insert_ip(str ip)
// creates a new ip address entry
function phpss_insert_ip($ip) {

	// check if entry already exists
	if (phpss_get_ip_id($ip) != false)
		return false;

	// insert entry
	phpss_db_query("INSERT INTO phpss_ip (ip) VALUES ('" . $ip . "')");

	return true;
}

?>
