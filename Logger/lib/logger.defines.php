<?php if (!defined('APPLICATION')) exit();
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
define('LOGGER_PLUGIN_VIEW_PATH', LOGGER_PLUGIN_LIB_PATH . '/views');

// URLs
define('LOGGER_PLUGIN_BASE_URL', '/plugin/logger');
define('LOGGER_APPENDERS_LIST_URL', LOGGER_PLUGIN_BASE_URL);
define('LOGGER_APPENDER_ADD_URL', LOGGER_PLUGIN_BASE_URL . '/appenderadd');
define('LOGGER_APPENDER_EDIT_URL', LOGGER_PLUGIN_BASE_URL . '/appenderedit');
define('LOGGER_APPENDER_DELETE_URL', LOGGER_PLUGIN_BASE_URL . '/appenderdelete');
define('LOGGER_GENERALSETTINGS_URL', LOGGER_PLUGIN_BASE_URL . '/settings');

// Return Codes
define('LOGGER_OK', 0);
define('LOGGER_ERR_INVALID_APPENDER_ID', 1001);
//define('LOGGER_ERR_INVALID_TIMESTAMP', 1002);
//define('LOGGER_ERR_INVALID_SIGNATURE', 1003);
//define('LOGGER_ERR_INVALID_USER', 1004);

// Http Arguments
define('LOGGER_ARG_APPENDERID', 'apd_id');
define('LOGGER_ARG_APPENDERTYPE', 'apd_type');
//define('LOGGER_ARG_APPENDERDESCRIPTION', 'email');



// List of the Appenders made available by the plugin
// TODO Move the list of plugins to a single multi-dimensional array
define('LOGGER_APPENDER_TYPES',
			 serialize(array(
											 // Console Appender
											 'LoggerAppenderConsole' =>
													array('Alias' => 'ApdConsole',
																'Label' => 'Console',
																'Description' => 'Writes logging events to the <code>php://stdout</code> or the <code>php://stderr</code> stream, the former being the default target.',),
											 )
								)
			);


define('LOGGER_APPENDER_TYPES_OLD',
			 serialize(array(// Plugin Appenders
												'LoggerAppenderGraylog2' => 'Graylog2 Log Server',
												'LoggerAppenderVanillaDB' => 'Forum\'s Database',

												// Standard Log4php Appenders
												'LoggerAppenderConsole' => 'Console',
												'LoggerAppenderDailyFile' => 'Daily File',
												'LoggerAppenderEcho' => 'PHP\'s echo',
												'LoggerAppenderFile' => 'File',
												'LoggerAppenderMail' => 'Email',
												'LoggerAppenderMailEvent' => 'Email (Individual Log Events)',
												'LoggerAppenderMongoDB' => 'MongoDB',
												'LoggerAppenderNull' => 'Null',
												'LoggerAppenderPDO' => 'External Database',
												'LoggerAppenderPhp' => 'PHP User-Level message',
												'LoggerAppenderRollingFile' => 'Rolling file',
												'LoggerAppenderSocket' => 'Network Socket',
												'LoggerAppenderSyslog' => 'Syslog',
											 )
								)
			);

// List of the Appenders Types descriptions
define('LOGGER_APPENDER_TYPES_DESCRIPTIONS',
			 serialize(array(// Plugin Appenders
												'LoggerAppenderGraylog2' => 'Sends Log to a <a href="http://graylog2.org/">Graylog2</a> instance.',
												'LoggerAppenderVanillaDB' => 'Saves Log to a table in Vanilla Database',

												// Standard Log4php Appenders
												'LoggerAppenderConsole' => 'Writes logging events to the <code>php://stdout</code> or the <code>php://stderr</code> stream, the former being the default target.',
												'LoggerAppenderDailyFile' => 'Writes logging events to a file. The file is rolled over once a day. In other words, for each day a new file is created.',
												'LoggerAppenderEcho' => 'Writes logging events using PHP\'s <code>echo()</code> function. Echo outputs may be buffered.',
												'LoggerAppenderFile' => 'Writes logging events to a file.',
												'LoggerAppenderMail' => 'Appends log events via email.',
												'LoggerAppenderMailEvent' => 'Appends individual log events via email.',
												'LoggerAppenderMongoDB' => 'Appends log events to a mongoDB instance.',
												'LoggerAppenderNull' => 'Ignores all logging requests; it never outputs a message to any device.',
												'LoggerAppenderPDO' => 'Appender logs to a database using the PHP\'s PDO extension. Use this Appender to connect to a Database which is not Vanilla\'s one.',
												'LoggerAppenderPhp' => 'Logs events by creating a PHP user-level message using the PHP\'s <code>trigger_error()</code> function.',
												'LoggerAppenderRollingFile' => 'Writes logging events to a specified file. The file is rolled over after a specified size has been reached.',
												'LoggerAppenderSocket' => 'Appends to a network socket.',
												'LoggerAppenderSyslog' => 'Logs events to the syslog. It is not recommended to use this logger if your websites runs on a shared server, as the SysLog is a single one on each server and could be accessible by other Users.',
											 )
								)
			);

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

	public static function GetLoggerAppenderInfo($AppenderClass) {
		return self::GetFromSerializedArray(LOGGER_APPENDER_TYPES, $AppenderClass);
	}

	public static function GetLoggerAppenderAttribute($AppenderClass, $AttributeName) {
		$AppenderInfo = &self::GetLoggerAppenderInfo($AppenderClass);
		return $AppenderInfo[$AttributeName];
	}

	/**
	 * Convenience function to retrieve the label from the serialized array
	 * of available Appender Types.
	 *
	 * @param AppenderClass The class of the Appender for which the description
	 * should be retrieved.
	 * @return The Appender's label, or null if Appender Class doesn't exist
	 * in array LOGGER_AVAILABLE_APPENDER_TYPES.
	 */
	public static function GetLoggerAppenderTypeLabel($AppenderClass) {
		return self::GetLoggerAppenderAttribute($AppenderClass, 'Label');
	}

	/**
	 * Convenience function to retrieve the description from the serialized array
	 * of available Appender Types.
	 *
	 * @param AppenderClass The class of the Appender for which the description
	 * should be retrieved.
	 * @return The Appender's description, or null if Appender Class doesn't exist
	 * in array LOGGER_AVAILABLE_APPENDER_TYPES_DESCRIPTIONS.
	 */
	public static function GetLoggerAppenderTypeDescription($AppenderClass) {
		return self::GetLoggerAppenderAttribute($AppenderClass, 'Description');
	}

	/**
	 * Returns an associative array containing all Appender Types and their Labels.
	 *
	 * @return An associative array containing all Appender Types and their Labels.
	 */
	public static function GetAppenderTypes() {
		$Result = array();
		foreach(unserialize(LOGGER_APPENDER_TYPES) as $AppenderClass => $AppenderInfo) {
			$Result[$AppenderClass] = $AppenderInfo['Label'];
		}

		return $Result;
	}

	/**
	 * Returns an associative array containing all Appender Types and their Labels.
	 *
	 * @return An associative array containing all Appender Types and their Labels.
	 */
	public static function GetAppenderTypesDescriptions() {
		$Result = array();
		foreach(unserialize(LOGGER_APPENDER_TYPES) as $AppenderClass => $AppenderInfo) {
			$Result[$AppenderClass] = $AppenderInfo['Description'];
		}

		return $Result;
	}
}
