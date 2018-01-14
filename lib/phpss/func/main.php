<?php

/*
 * phpSecureSite
 *
 * func/main.php
 *
 * Main phpSecureSite functions
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

// int phpss_auth()
// Makes sure the session is valid, and sets a few vars if so.
// Return values are defined in vars/constants.php.
function phpss_auth() {
	global $phpss_reg, $phpss_session_key, $phpss_account_id, $phpss_session_valid;
	$cfg = phpss_module_config_get("phpss");

	// explicitly unset various settings
	$phpss_session_key = "";
	$phpss_account_id = "";
	$phpss_session_valid = false;

	// emit precheck event
	if (($status = phpss_event("session_validate_precheck")) != "")
		return $status;

	// check that the cookie is set
	if (isset($_COOKIE[$cfg["cookie"]["name"]]) == false) {
		phpss_log("phpss", "Page request without session cookie");
		return PHPSS_AUTH_NOCOOKIE;
	}

	// copy session key into a variable
	$sessionkey = $_COOKIE[$cfg["cookie"]["name"]];

	// emit postfetch event
	if (($status = phpss_event("session_validate_cookiefetch", array("sessionkey" => $sessionkey))) != "")
		return $status;

	// check that the session key exists in the database, and fetch session data
	if (($sessiondata = phpss_get_session_data($sessionkey)) == false || strtolower($sessiondata["active"]) == "false") {
		phpss_log("phpss", "Page request with invalid session key ('" . $sessionkey . "')");
		return PHPSS_AUTH_INVKEY;
	}

	// emit postcheck event
	if (($status = phpss_event("session_validate_postcheck", array("sessionkey" => $sessionkey))) != "")
		return $status;

	// set last request timestamp to current time
	phpss_set_session_lastrequest($sessionkey, date("Y-m-d H:i:s"));

	// set some global vars
	$phpss_session_key = $sessionkey;
	$phpss_account_id = $sessiondata["accountid"];
	$phpss_session_valid = true;

	// emit success event
	if (($status = phpss_event("session_validate_success", array("sessionkey" => $sessionkey))) != "")
		return $status;

	return PHPSS_AUTH_ALLOW;
}

// void phpss_init(arr config)
// initializes phpSecureSite
function phpss_init($config) {

	// define constants

	// phpss_auth() return values
	define("PHPSS_AUTH_ALLOW", 1);		// User is authorized for access

	define("PHPSS_AUTH_NOCOOKIE", -1);	// No cookie found
	define("PHPSS_AUTH_INVKEY", -2);	// Invalid session key (non-existant or deactived session)

	define("PHPSS_AUTH_TIMEOUT", -16);	// Session timed out (timeout module)
	define("PHPSS_AUTH_HIJACK", -17);	// Session hijack attempt (hijack module)
	define("PHPSS_AUTH_ACLDENY", -18);	// Access denied by access control list (acl module)
	define("PHPSS_AUTH_IPACCESS_DENY", -19);	// Access denied from this ip address (ipaccess module)

	// phpss_login() return values (if successful, phpss_login() returns the account id)
	define("PHPSS_LOGIN_AUTHFAIL", -1);		// Client provided an invalid username/password combination
	define("PHPSS_LOGIN_BRUTEFORCE_LOCK_SRCIP", -16);	// Logins from this IP have been locked (bruteforce module)
	define("PHPSS_LOGIN_BRUTEFORCE_LOCK_ACCOUNT", -17);	// Logins for this account have been locked (bruteforce module)
	define("PHPSS_LOGIN_IPACCESS_DENY", -18);	// login not allowed from this ip address (ipaccess module)

	// module types
	define("PHPSS_MODULE_TYPE_NORMAL", 1);
	define("PHPSS_MODULE_TYPE_DBBACKEND", 2);
	define("PHPSS_MODULE_TYPE_AUTHHANDLER", 3);
	define("PHPSS_MODULE_TYPE_LOGBACKEND", 4);

	// handler types
	define("PHPSS_HANDLER_TYPE_DB_CONNECT", 1);
	define("PHPSS_HANDLER_TYPE_DB_QUERY", 2);
	define("PHPSS_HANDLER_TYPE_LOG_MESSAGE", 3);

	define("PHPSS_HANDLER_TYPE_AUTH_LOGIN", 4);
	define("PHPSS_HANDLER_TYPE_AUTH_ACCOUNTDATA", 5);
	define("PHPSS_HANDLER_TYPE_AUTH_GROUPDATA", 6);
	define("PHPSS_HANDLER_TYPE_AUTH_ACCOUNTGROUPS", 7);
	define("PHPSS_HANDLER_TYPE_AUTH_ACCOUNTID", 8);
	define("PHPSS_HANDLER_TYPE_AUTH_GROUPID", 9);

	// register phpss (system) module and events
	phpss_module_register("phpss");
	phpss_event_register("system_init");
	phpss_event_register("session_create_precheck");
	phpss_event_register("session_create_authhandler");
	phpss_event_register("session_create_fail");
	phpss_event_register("session_create_postcheck");
	phpss_event_register("session_create_complete");
	phpss_event_register("session_validate_precheck");
	phpss_event_register("session_validate_cookiefetch");
	phpss_event_register("session_validate_postcheck");
	phpss_event_register("session_validate_success");
	phpss_event_register("session_close_start");
	phpss_event_register("session_close_validkey");
	phpss_event_register("session_close_complete");

	// set up system config
	phpss_module_config_store("phpss", $config["phpss"]);

	// load database backend
	$dbbackend = $config["phpss"]["dbbackend"];
	phpss_module_load($config["dbbackend"][$dbbackend]["modulefile"]);
	phpss_module_config_store($dbbackend, $config["dbbackend"][$dbbackend], PHPSS_MODULE_TYPE_DBBACKEND);

	// load authentication handler
	$authhandler = $config["phpss"]["authhandler"];
	phpss_module_load($config["authhandler"][$authhandler]["modulefile"]);
	phpss_module_config_store($authhandler, $config["authhandler"][$authhandler], PHPSS_MODULE_TYPE_AUTHHANDLER);

	// load log backend
	if ($config["phpss"]["logging"] == true) {
		$logbackend = $config["phpss"]["logbackend"];
		phpss_module_load($config["logbackend"][$logbackend]["modulefile"]);
		phpss_module_config_store($logbackend, $config["logbackend"][$logbackend], PHPSS_MODULE_TYPE_LOGBACKEND);
	}

	// load other modules
	while(list($module, $module_config) = each($config["module"])) {
		if ($module_config["enable"] == true) {
			phpss_module_load($module_config["modulefile"]);
			phpss_module_config_store($module, $module_config);

			// initialize module
			phpss_module_init($module);
		}
	}

}

// void phpss_log(str module, str message)
// logs an event.
// This is the main interface to the logging subsystem.
function phpss_log ($module, $message) {
	$cfg = phpss_module_config_get("phpss");
	global $phpss_reg;

	if ($cfg["logging"] == true) {

		// check if the module is loaded
		if(phpss_module_exists($module) == false)
			phpss_error("Log message received from unknown module '" . $module . "'");

		// execute log handler
		$sessionkey = isset($_COOKIE[$cfg["cookie"]["name"]]) == true ? $_COOKIE[$cfg["cookie"]["name"]] : "";
		call_user_func($phpss_reg["logbackend"]["handler"], $module, date("Y-m-d H:i:s"), $sessionkey, $_SERVER["REMOTE_ADDR"], $message);
	}
}

// bool phpss_login(str username, str password)
// attempts to log a user in, and creates a
// session if successful
function phpss_login($username, $password) {
	global $phpss_reg;
	$cfg = phpss_module_config_get("phpss");

	// emit precheck event
	if (($status = phpss_event("session_create_precheck", array("username" => $username, "password" => $password))) != "")
		return $status;

	// attempt to authenticate user
	$accountid = phpss_authenticate($username, $password);

	// emit authhandler event
	if (($status = phpss_event("session_create_authhandler", array("accountid" => $accountid, "username" => $username, "password" => $password))) != "")
		return $status;

	// authentication failed
	if ($accountid == false) {
		phpss_log("phpss", "Failed login attempt (username '" . $username . "')");
		phpss_event("session_create_fail", array("username" => $username, "password" => $password));
		return PHPSS_LOGIN_AUTHFAIL;
	}

	// emit postcheck event
	if (($status = phpss_event("session_create_postcheck", array("accountid" => $accountid))) != "")
		return $status;

	// create session, and set $_COOKIE superglobal variable to session key
	// (so it is available for logging etc)
	$_COOKIE[$cfg["cookie"]["name"]] = phpss_session_setup($accountid);

	phpss_log("phpss", "Successful user login");

	// emit complete event
	if (($status = phpss_event("session_create_complete", array("accountid" => $accountid))) != "")
		return $status;

	return $accountid;
}

// bool phpss_logout([str sessionkey])
// logs a user out
function phpss_logout($sessionkey = "") {
	global $phpss_reg;
	$cfg = phpss_module_config_get("phpss");

	// default to cookie value
	if ($sessionkey == "")
		$sessionkey = $_COOKIE[$cfg["cookie"]["name"]];

	// emit precheck event
	if (($status = phpss_event("session_close_start", array("sessionkey" => $sessionkey))) != "")
		return $status;

	// check if session is valid
	// (if not a user without a valid session can fill up the logs with
	// fake logout messages, possible creating a DoS condition)
	if (($sessiondata = phpss_get_session_data($sessionkey)) == false || $sessiondata["active"] == "false")
		return false;

	// emit validkey event
	if (($status = phpss_event("session_close_validkey", array("sessionkey" => $sessionkey))) != "")
		return $status;

	
	// update database record status
	phpss_set_session_status($sessionkey, false);

	// delete cookie
	$c = $cfg["cookie"]; // yes, I'm lazy :)
	setcookie($c["name"], "", 1, $c["path"], $c["domain"], $c["secure"]);

	phpss_log("phpss", "User logout");

	// emit complete event
	if (($status = phpss_event("session_close_complete", array("sessionkey" => $sessionkey))) != "")
		return $status;

	return true;
}

?>
