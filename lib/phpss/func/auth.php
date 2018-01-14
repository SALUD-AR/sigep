<?php

/*
 * phpSecureSite
 *
 * func/auth.php
 *
 * Authentication-related functions
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


// int phpss_authenticate(str username, str password)
// executes the authhandler login handler
function phpss_authenticate($username, $password) {
	global $phpss_reg;
	return call_user_func($phpss_reg["authhandler"]["login"], $username, $password);
}

// arr phpss_get_account_data(int accountid)
// retrieves all data about an account
function phpss_get_account_data($accountid) {
	global $phpss_reg;
	return call_user_func($phpss_reg["authhandler"]["accountdata"], $accountid);
}

// arr phpss_get_account_groups(int accountid)
// fetches an array of the groups which the account is a member of
function phpss_get_account_groups($accountid) {
	global $phpss_reg;
	return call_user_func($phpss_reg["authhandler"]["accountgroups"], $accountid);
}

// int phpss_get_account_id(str username)
// fetches an accounts id
function phpss_get_account_id($username) {
	global $phpss_reg;
	return call_user_func($phpss_reg["authhandler"]["accountid"], $username);
}

// arr phpss_get_group_data(int groupid)
// retrieves data about a group
function phpss_get_group_data($groupid) {
	global $phpss_reg;
	return call_user_func($phpss_reg["authhandler"]["groupdata"], $groupid);
}

// int phpss_get_group_id(str groupname)
// looks up the id of a group based on the group name
function phpss_get_group_id($groupname) {
	global $phpss_reg;
	return call_user_func($phpss_reg["authhandler"]["groupid"], $groupname);
}

?>
