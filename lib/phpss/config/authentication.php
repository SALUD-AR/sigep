<?php

/*
 * phpSecureSite
 *
 * config/authentication.php
 *
 * Configuration file for authentication handlers. Detailed explanations
 * of config options and modules are available in the documentation.
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


// Set the name of the authentication handler to use here.
// Leave this as "database" unless you *really* know what you're doing.
// You only need to configure the authentication handler that you
// define here.
$phpss_cfg["phpss"]["authhandler"] = "database";




/*
 * database authhandler - authentication against the standard phpss database
 *
 * This authhandler performs authentication over the internal phpSecureSite
 * database connection, against the standard phpsecuresite database.
 *
 * Normally, you REALLY want to use this one!
 *
*/

$phpss_cfg["authhandler"]["database"] = array(
	"modulefile"		=> PHPSS_ROOT_FS . "/modules/authhandlers/database.php",

	// The format of passwords in the database. Valid values are:
	// plaintext : as-is, no special handling
	// md5       : encrypted using the md5 one-way hash algorithm
	// mysqlpw   : encrypted using mysqls password() function (mysql only)
	"pwtype"		=> "md5"
);




/*
 * mysql authhandler - authentication against a mysql server
 *
 * This authhandler performs authentication against a MySQL database server.
 * It is meant for authentication against a different database server than the
 * one you are using for phpSecureSite.
 *
 * WARNING: If you use the normal phpsecuresite database for account data,
 * then please use the database authhandler instead.
 *
*/

$phpss_cfg["authhandler"]["mysql"] = array(
	"modulefile"		=> PHPSS_ROOT_FS . "/modules/authhandlers/mysql.php",

	// database server address (fully-qualified domain name or ip address)
	"hostname"		=> "localhost",

	// tcp port number to connect to (mysql uses 3306 by default)
	"port"			=> 3306,

	// username and password to connect with
	"username"		=> "notroot",
	"password"		=> "secret",

	// the database to use
	"database"		=> "account",


	// the format of passwords. valid values are:
	// plaintext : as-is, no special handling
	// md5       : encrypted using the md5 one-way hash algorithm
	// mysqlpw   : encrypted using mysqls password() function
	"pwtype"		=> "mysqlpw",


	// table and column names for accounts
	"tab_account"		=> "accounts",
	"col_account_id"	=> "id",
	"col_account_username"	=> "username",
	"col_account_password"	=> "password",
	"col_account_active"	=> "active",

	// table and column names for groups
	"tab_group"		=> "groups",
	"col_group_id"		=> "id",
	"col_group_name"	=> "name",

	// table and column names for account/group links
	"tab_account_group"	=> "account_group",
	"col_account_group_accountid"	=> "accountid",
	"col_account_group_groupid"	=> "groupid"
);




/*
 * pgsql authhandler - authentication against a postgresql server
 *
 * This authhandler performs authentication against a PostgreSQL database
 * server. It is meant for authentication against a different database server
 * than the one you are using for phpSecureSite.
 *
 * WARNING: If you use the normal phpsecuresite database for account data,
 * then please use the database authhandler instead.
 *
*/

$phpss_cfg["authhandler"]["pgsql"] = array(
	"modulefile"		=> PHPSS_ROOT_FS . "/modules/authhandlers/pgsql.php",

	// database server address (fully-qualified domain name or ip address)
	"hostname"		=> "localhost",

	// tcp port number to connect to (postgresql uses 5432 by default)
	"port"			=> 5432,

	// username and password to connect with
	"username"		=> "notroot",
	"password"		=> "secret",

	// the database to use
	"database"		=> "phpss",


	// the format of passwords in the database. valid values are:
	// plaintext : as-is, no special handling
	// md5       : encrypted using the md5 one-way hash algorithm
	"pwtype"		=> "md5",


	// table and column names for accounts
	"tab_account"		=> "accounts",
	"col_account_id"	=> "id",
	"col_account_username"	=> "username",
	"col_account_password"	=> "password",
	"col_account_active"	=> "active",

	// table and column names for groups
	"tab_group"		=> "groups",
	"col_group_id"		=> "id",
	"col_group_name"	=> "name",

	// table and column names for account/group links
	"tab_account_group"	=> "account_group",
	"col_account_group_accountid"	=> "accountid",
	"col_account_group_groupid"	=> "groupid"
);

?>
