<?php

/*
 * phpSecureSite
 *
 * func/module.php
 *
 * Functions for the module infrastructure
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

// void phpss_handler_register(int type, str function)
// registers a handler function
function phpss_handler_register($type, $function) {
	global $phpss_reg;

	// check if the function is defined
	if (function_exists($function) == false)
		phpss_error("No such function '" . $function . "' when registering handler");

	// register handler
	switch ($type) {
		case PHPSS_HANDLER_TYPE_DB_CONNECT: $phpss_reg["dbbackend"]["connect"] = $function; break;
		case PHPSS_HANDLER_TYPE_DB_QUERY: $phpss_reg["dbbackend"]["query"] = $function; break;
		case PHPSS_HANDLER_TYPE_LOG_MESSAGE: $phpss_reg["logbackend"]["handler"] = $function; break;
		case PHPSS_HANDLER_TYPE_AUTH_LOGIN: $phpss_reg["authhandler"]["login"] = $function; break;
		case PHPSS_HANDLER_TYPE_AUTH_ACCOUNTDATA: $phpss_reg["authhandler"]["accountdata"] = $function; break;
		case PHPSS_HANDLER_TYPE_AUTH_ACCOUNTGROUPS: $phpss_reg["authhandler"]["accountgroups"] = $function; break;
		case PHPSS_HANDLER_TYPE_AUTH_ACCOUNTID: $phpss_reg["authhandler"]["accountid"] = $function; break;
		case PHPSS_HANDLER_TYPE_AUTH_GROUPDATA: $phpss_reg["authhandler"]["groupdata"] = $function; break;
		case PHPSS_HANDLER_TYPE_AUTH_GROUPID: $phpss_reg["authhandler"]["groupid"] = $function; break;
		default: phpss_error("Invalid handler type when registering handler '" . $function . "'");
	}
}

// arr phpss_module_config_get(str module[, int type])
// retrieves a module configuration array
function phpss_module_config_get($module, $type = PHPSS_MODULE_TYPE_NORMAL) {
	global $phpss_reg;

	// check if module exists
	if (phpss_module_exists($module, $type) == false)
		phpss_error("Unable to retrieve config for module '" . $module . "': no such module");

	switch($type) {
		case PHPSS_MODULE_TYPE_NORMAL: return $phpss_reg["modules"][$module]["cfg"]; break;
		case PHPSS_MODULE_TYPE_DBBACKEND: return $phpss_reg["dbbackend"]["cfg"]; break;
		case PHPSS_MODULE_TYPE_AUTHHANDLER: return $phpss_reg["authhandler"]["cfg"]; break;
		case PHPSS_MODULE_TYPE_LOGBACKEND: return $phpss_reg["logbackend"]["cfg"]; break;
		default: phpss_error("Invalid module type for '" . $module . "' when retreiving config");
	}
}

// void phpss_module_config_store(str module, arr config[, int type])
// stores a module's configuration array
function phpss_module_config_store($module, $config, $type = PHPSS_MODULE_TYPE_NORMAL) {
	global $phpss_reg;

	// check if module exists
	if (phpss_module_exists($module, $type) == false)
		phpss_error("Unable to store config for module '" . $module . "': no such module");

	// store configuration
	switch($type) {
		case PHPSS_MODULE_TYPE_NORMAL: $phpss_reg["modules"][$module]["cfg"] = $config; break;
		case PHPSS_MODULE_TYPE_DBBACKEND: $phpss_reg["dbbackend"]["cfg"] = $config; break;
		case PHPSS_MODULE_TYPE_AUTHHANDLER: $phpss_reg["authhandler"]["cfg"] = $config; break;
		case PHPSS_MODULE_TYPE_LOGBACKEND: $phpss_reg["logbackend"]["cfg"] = $config; break;
		default: phpss_error("Unknown module type for '" . $module . "' when registering config");
	}
}

// bool phpss_module_exists(str module[, int type])
// checks if a module has been registered
function phpss_module_exists($module, $type = PHPSS_MODULE_TYPE_NORMAL) {
	global $phpss_reg;

	switch($type) {
		case PHPSS_MODULE_TYPE_NORMAL: return isset($phpss_reg["modules"][$module]);
		case PHPSS_MODULE_TYPE_DBBACKEND: return (isset($phpss_reg["dbbackend"]["module"]) && $phpss_reg["dbbackend"]["module"] == $module);
		case PHPSS_MODULE_TYPE_AUTHHANDLER: return (isset($phpss_reg["authhandler"]["module"]) && $phpss_reg["authhandler"]["module"] == $module);
		case PHPSS_MODULE_TYPE_LOGBACKEND: return (isset($phpss_reg["logbackend"]["module"]) && $phpss_reg["logbackend"]["module"] == $module);
		default: phpss_error("phpss_module_exists: unknown module type");
	}

}

// void phpss_module_init(str module)
// initializes the loaded module by calling the init handler
function phpss_module_init($module) {
	global $phpss_reg;

	if (isset($phpss_reg["modules"][$module]["inithandler"]) == true)
		call_user_func($phpss_reg["modules"][$module]["inithandler"]);
}

// void phpss_module_init_register(str module, str function)
// registers an init function which is called when the module is loaded
function phpss_module_init_register($module, $function) {
	global $phpss_reg;

	if (phpss_module_exists($module) == false)
		phpss_error("Unknown module when registering init handler");

	if (function_exists($function) == false)
		phpss_error("Undefined function when registering init handler");

	$phpss_reg["modules"][$module]["inithandler"] = $function;
}

// void phpss_module_load(str file)
// loads a module file
function phpss_module_load($file) {

	// check if file exists and is readable
	if (is_readable($file) == false)
		phpss_error("Unable to load file '" . $file . "'");
	
	// load module
	require($file);
}

// void phpss_module_register(str module[, int type])
// registers a module, type indicates module type (see constants)
function phpss_module_register($module, $type = PHPSS_MODULE_TYPE_NORMAL) {
	global $phpss_reg;

	// check if module already exists
	if(phpss_module_exists($module, $type) == true)
		phpss_error("A module named '" . $module . "' is already loaded");

	// register module
	switch($type) {
		case PHPSS_MODULE_TYPE_NORMAL:
			if (phpss_module_exists($module, $type) == true)
				phpss_error("A module named '" . $module . "' is already loaded");
			$phpss_reg["modules"][$module] = array();
			break;

		case PHPSS_MODULE_TYPE_DBBACKEND:
			if (phpss_module_type_loaded($type) == true)
				phpss_error("A database backend module already exists when loading '" . $module . "'");
			$phpss_reg["dbbackend"]["module"] = $module;
			break;

		case PHPSS_MODULE_TYPE_AUTHHANDLER:
			if (phpss_module_type_loaded($type) == true)
				phpss_error("An authentication handler module already exists when loading '" . $module . "'");
			$phpss_reg["authhandler"]["module"] = $module;
			break;

		case PHPSS_MODULE_TYPE_LOGBACKEND:
			if (phpss_module_type_loaded($type) == true)
				phpss_error("A log backend module already exists when loading '" . $module . "'");
			$phpss_reg["logbackend"]["module"] = $module;
			break;

		default:
			phpss_error("Module '" . $module . "' registration attempt with unknown module type");
	}
}

// bool phpss_module_type_loaded(int type)
// checks if a module type has been loaded
function phpss_module_type_loaded($type) {
	global $phpss_reg;

	switch ($type) {
		case PHPSS_MODULE_TYPE_NORMAL: return (sizeof($phpss_reg["modules"] > 0));
		case PHPSS_MODULE_TYPE_DBBACKEND: return isset($phpss_reg["dbbackend"]["module"]);
		case PHPSS_MODULE_TYPE_AUTHHANDLER: return isset($phpss_reg["authhandler"]["module"]);
		case PHPSS_MODULE_TYPE_LOGBACKEND: return isset($phpss_reg["logbackend"]["module"]);
		default: phpss_error("phpss_module_type_loaded: invalid module type");
	}
}

?>
