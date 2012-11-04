<?php if (!defined('APPLICATION')) exit();
/**
{licence}
*/

/**
 * Constants used by Logger Plugin.
 *
 * @package LoggerPlugin
 */

// Default Configuration Settings
define('LOGGER_DEFAULT_LOGLEVEL', 'INFO');

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
