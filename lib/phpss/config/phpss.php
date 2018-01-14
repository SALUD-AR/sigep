<?php

/*
 * phpSecureSite
 *
 * config/phpss.php
 *
 * Configuration file for the phpSecureSite system. Detailed explanations
 * of config options and modules are available in the documentation.
 *
 * Remember that configuration of database backends, authentication handlers
 * and logging backends are done in the files database.php, authentication.php
 * and logging.php respectively.
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


// Cookie settings (for session cookie)
$phpss_cfg["phpss"]["cookie"] = array(

	// The name of the cookie
	"name"		=> "sk",

	// When the cookie expires (0 = when browser closes)
	// Note: for session timeouts, have a look at the timeout module
	"expire"	=> 0,

	// The URL path under which the cookie is valid
	"path"		=> "/",

	// The domain where the cookie is valid (defaults to server hostname)
	"domain"	=> "",

	// Whether to use SSL for cookie transfers (1 = yes, 0 = no)
	"secure"	=> 0
);


// load dbbackend, authhandler and logbackend configuration
require(PHPSS_ROOT_FS . "/config/database.php");
require(PHPSS_ROOT_FS . "/config/authentication.php");
require(PHPSS_ROOT_FS . "/config/logging.php");




// Module configuration follows. To activate a module, you must set
// the "enable" option to true.


/*
 * acl module - access control lists
 *
 * ACLs lets you set up access control on specific web-pages. You can
 * define rules for accounts and groups. See the documentation for more
 * information on how to set up and use the ACLs.
 *
*/

$phpss_cfg["module"]["acl"] = array(
	"enable"		=> false,
	"modulefile"		=> PHPSS_ROOT_FS . "/modules/acl.php",

	// This sets the access policy. It is used as a fallback when
	// no matching access rules are found. Valid values are "allow"
	// and "deny".
	"policy"		=> "allow",

	// The preferred access to use when an account is a member of
	// several groups, and these have conflicting access rules.
	// Valid values are "allow" and "deny"
	"preferred"		=> "allow"
);




/*
 * bruteforce module - brute force protection
 *
 * This protects against brute force attacks, where an attacker tries
 * different random combinations of usernames and/or passwords in order
 * to gain access. This will lock (ie deny access to) an account and/or
 * ip address after a number of successive failed logins. If a login is
 * successful, the counters are reset to zero.
 *
*/

$phpss_cfg["module"]["bruteforce"] = array(
	"enable"		=> true,
	"modulefile"		=> PHPSS_ROOT_FS . "/modules/bruteforce.php",

	// Account-based protection
	"account"		=> array(

		// (De)activate account-based protection
		"enable"	=> true,

		// Number of failed logins before account is locked
		"threshold"	=> 5,

		// The period of time, in seconds, in which the threshold must
		// be reached for the account to be locked.
		"thresholdtime"	=> 300,

		// How long a lock should be active, in seconds (0 is forever)
		"locktime"	=> 1800
	),


	// Source IP address based protection
	"ip"			=> array(

		// (De)activate source IP based protection
		"enable"	=> true,

		// Number of failed logins before the source IP is locked
		"threshold"	=> 10,

		// The period of time, in seconds, in which the threshold must
		// be reached for the account to be locked.
		"thresholdtime"	=> 300,

		// How long a lock should be active, in seconds (0 is forever)
		"locktime"	=> 3600
	)
);




/*
 * cachecontrol module - control cache policies through http headers
 *
 * Lets you define caching policies. These will set a few HTTP headers to
 * tell browsers and proxies how to handle caching for the document.
 *
*/

$phpss_cfg["module"]["cachecontrol"] = array(
	"enable"		=> false,
	"modulefile"		=> PHPSS_ROOT_FS . "/modules/cachecontrol.php",

	// Cache policy; valid values are:
	// nocache : never cache the document
	// private : private caches may be used (browser, private proxy etc)
	// public  : any cache, including shared proxies, may be used
	"policy"		=> "nocache",

	// Whether the cached copy should always be revalidated against the
	// server. Recommended.
	"revalidate"		=> true
);




/*
 * hijack module - prevents session hijacking
 *
 * Session hijacking is when an attacker gets hold of the session key stored in
 * the cookie. He can then set the session key in his own browser, and gain
 * access to the account.
 *
 * This module checks the source ip address of every request, and if it is
 * different than the one the user logged in from, access is denied. Note that
 * there may be valid reasons for why an ip address might change, suck as dynamic
 * address (dhcp) or round-robin web proxy systems.
 *
*/

$phpss_cfg["module"]["hijack"] = array(
	"enable"		=> false,
	"modulefile"		=> PHPSS_ROOT_FS . "/modules/hijack.php"

	// no config options - it just works
);




/*
 * ipaccess module - set access based on the source ip
 *
 * With this, you can control which IP addresses are allowed access to the
 * application. Access definitions are set in the database, in the
 * phpss_ipaccess table.
 *
*/

$phpss_cfg["module"]["ipaccess"] = array(
	"enable"		=> false,
	"modulefile"		=> PHPSS_ROOT_FS . "/modules/ipaccess.php",

	// When to perform IP checks. Valid values are:
	// login  : checks are performed when the user logs in
	// always : checks are performed at each page request
	//
	// "login" should always be used together with the hijack module
	"checktime"		=> "always",

	// Default action, when no matching rule is found ("allow" or "deny")
	"policy"		=> "allow",

	// Preferred access to use when conflicting rules are found
	// (for example when an account is a member of several groups,
	// and these groups have different access rules)
	"preferred"		=> "allow"
);




/*
 * loginfaildelay module - delays failed login messages
 *
 * Sets a delay before "login denied" messages are displayed. This can be
 * used to slow down password guessing attempts. Successful logins are not
 * affected.
 *
 * See also the bruteforce module.
 *
*/

$phpss_cfg["module"]["loginfaildelay"] = array(
	"enable"		=> false,
	"modulefile"		=> PHPSS_ROOT_FS . "/modules/loginfaildelay.php",

	// The length of the delay, in seconds
	"delay"			=> 3
);




/*
 * svars module - session variable functionality
 *
 * Session variables are used to store data for a specific session,
 * so that you can retrieve that data in another page request. Often
 * used for shopping cart functionality, amongst other things.
 *
*/

$phpss_cfg["module"]["svars"] = array(
	"enable"		=> true,
	"modulefile"		=> PHPSS_ROOT_FS . "/modules/svars.php",

	// Enabling this will make phpsecuresite automatically set all
	// session variables in the global scope at the start of a
	// page request (when phpss_auth() is called)
	"autoset"		=> true
);




/*
 * timeout module - session timeouts
 *
 * Session timeouts will mark a session as invalid after a defined period
 * of inactivity.
 *
*/

$phpss_cfg["module"]["timeout"] = array(
	"enable"		=> true,
	"modulefile"		=> PHPSS_ROOT_FS . "/modules/timeout.php",

	// The session timeout limit, in minutes
	"timeout_limit"		=> $session_timeout
);




/*
 * usertrack module - records user activity
 *
 * This module records every page request, and marks it with the session
 * which the request was made with. You can then see how a user moved around
 * on the site. Some sort of analysis tool should be run on the data, to generate
 * statistics etc, but no such tool is included with phpSecureSite.
 *
*/

$phpss_cfg["module"]["usertrack"] = array(
	"enable"		=> false,
	"modulefile"		=> PHPSS_ROOT_FS . "/modules/usertrack.php"

	// no config options - it just works
);

?>
