<?php if (!defined('APPLICATION')) exit();
/**
{licence}
*/

/**
 * Copyright 2012 Diego Zanella
 * This file is part of Logger Plugin for Vanilla Forums.
 *
 * Plugin is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or (at your
 * option) any later version.
 * Plugin is distributed in the hope that it will be
 * useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General
 * Public License for more details.
 * You should have received a copy of the GNU General Public License along with
 * Logger Plugin. If not, see http://opensource.org/licenses/GPL-2.0.
 *
 * @package Logger Plugin
 * @author Diego Zanella <diego@pathtoenlightenment.net>
 * @copyright Copyright (c) 2011 Diego Zanella (http://dev.pathtoenlightenment.net)
 * @license http://opensource.org/licenses/GPL-2.0 GPL 2.0
*/

/**
 * Constants used by Logger Plugin.
 *
 * @package LoggerPlugin
 */

// Default Configuration Settings

// Paths
define('LOGGER_PLUGIN_PATH', PATH_PLUGINS . '/Logger');
define('LOGGER_PLUGIN_LIB_PATH', LOGGER_PLUGIN_PATH . '/lib');
define('LOGGER_PLUGIN_CLASS_PATH', LOGGER_PLUGIN_LIB_PATH . '/classes');
define('LOGGER_PLUGIN_MODEL_PATH', LOGGER_PLUGIN_CLASS_PATH . '/models');
define('LOGGER_PLUGIN_EXTERNAL_PATH', LOGGER_PLUGIN_LIB_PATH . '/external');
define('LOGGER_PLUGIN_VIEW_PATH', LOGGER_PLUGIN_PATH . '/views');
define('LOGGER_PLUGIN_ETC_PATH', LOGGER_PLUGIN_PATH . '/etc');
define('LOGGER_PLUGIN_CERTS_PATH', LOGGER_PLUGIN_ETC_PATH . '/certificates');

// URLs
define('LOGGER_PLUGIN_BASE_URL', '/plugin/logger');
//define('LOGGER_APPENDERS_LIST_URL', LOGGER_PLUGIN_BASE_URL);
define('LOGGER_APPENDERS_LIST_URL', LOGGER_PLUGIN_BASE_URL . '/appenders');
define('LOGGER_APPENDER_ADD_URL', LOGGER_PLUGIN_BASE_URL . '/appenderadd');
define('LOGGER_APPENDER_EDIT_URL', LOGGER_PLUGIN_BASE_URL . '/appenderedit');
define('LOGGER_APPENDER_DELETE_URL', LOGGER_PLUGIN_BASE_URL . '/appenderdelete');
define('LOGGER_GENERALSETTINGS_URL', LOGGER_PLUGIN_BASE_URL . '/settings');
define('LOGGER_TESTLOG_URL', LOGGER_PLUGIN_BASE_URL . '/testlog');
define('LOGGER_APPENDER_ENABLE_URL', LOGGER_PLUGIN_BASE_URL . '/appenderenable');
define('LOGGER_VIEW_LOG_URL', LOGGER_PLUGIN_BASE_URL . '/viewlog');

// Return Codes
define('LOGGER_OK', 0);
define('LOGGER_ERR_INVALID_APPENDER_ID', 1001);
//define('LOGGER_ERR_INVALID_TIMESTAMP', 1002);
//define('LOGGER_ERR_INVALID_SIGNATURE', 1003);
//define('LOGGER_ERR_INVALID_USER', 1004);

// Http Arguments
define('LOGGER_ARG_APPENDERID', 'apd_id');
define('LOGGER_ARG_ENABLEFLAG', 'enable');
define('LOGGER_ARG_APPENDERTYPE', 'apd_type');
//define('LOGGER_ARG_APPENDERDESCRIPTION', 'email');

// Definitions for Log4php configuration files
define('LOGGER_LOG4PHP_ROOTLOGGER', 'rootLogger');
define('LOGGER_LOG4PHP_APPENDERS', 'appenders');

/**
 * Auxiliary class to handled serialized arrays declared using "define".
 */
class LoggerConst {
	/**
	 * Generic function to retrieve a value from a serialized array.
	 *
	 * @param SerializedArray The serialized array from which the value should be
	 * retrieved.
	 * @param Key the Key which will be used to retrieve the value.
	 * @return A value from the serialized array, or null if the array doesn't
	 * exist, or the Key is not found.
	 */
	protected static function GetFromSerializedArray($SerializedArray, $Key) {
		if(empty($SerializedArray)) {
			return null;
		}
		$Values = unserialize($SerializedArray);
		return $Values[$Key];
	}
}
