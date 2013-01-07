<?php if (!defined('APPLICATION')) exit();
/**
{licence}
*/

/**
 * Logger Configuration Model
 *
 * @package LoggerPlugin
 */

/**
 * This model is used to retrieve the configuration of Appenders, Filters,
 * Loggers, etc. and use them produce an associative array that can be passed
 * to Log4php for initialization.
 */
class LoggerConfigModel extends Gdn_Model {
	/// Stores the configuration that will be passed to Log4php
	private $LoggerConfig = array();

	/**
	 * Defines the related database table name.
	 *
	 */
	public function __construct() {
		parent::__construct('LoggerConfig');
	}

	/**
	 * Initializes the associative array containing Logger Configuration by adding
	 * and setting the entries that must always appear in it.
	 *
	 * @return void.
	 */
	protected function InitializeLoggerSection($LoggerName = LOGGER_LOG4PHP_ROOTLOGGER) {
		// Initialize
		$this->LoggerConfig[$LoggerName] = array();
		$this->LoggerConfig[$LoggerName]['level'] = C('Plugin.Logger.LogLevel');
		$this->LoggerConfig[$LoggerName]['appenders'] = array();
	}

	/**
	 * Adds the settings for a Log Appender to the global Logger configuration.
	 *
	 * @param AppenderName The name of the Appender to whom the settings belong.
	 * @param Settings An associative array of settings, in the format specified
	 * in <a href="http://logging.apache.org/log4php/docs/configuration.html">Log4php documentation</a>.
	 * @return void.
	 */
	protected function AddAppenderSettings($AppenderName, array $Settings) {
		// Add the Appender to the global list of configured Appenders
		$this->LoggerConfig[LOGGER_LOG4PHP_APPENDERS][$AppenderName] = $Settings;
	}

	/**
	 * Loads the settings of all active Appenders in the associative array that
	 * will contain the global Logger configuration.
	 *
	 * @return void.
	 */
	protected function LoadAppendersSettings() {
		$BaseAppenderCfgModel = new LoggerAppenderConfigModel();
		$ActiveAppenders = $BaseAppenderCfgModel->GetActiveAppenders();

		foreach($ActiveAppenders as $AppenderCfg) {
			// Load the model that will retrieve the Appender's settings
			$AppenderConfigModel = LoggerPlugin::AppendersManager()->GetModel($AppenderCfg->AppenderClass);

			// Get Appender's settings in the format expected by Log4php and add them
			// to the global configuration array
			$this->AddAppenderSettings($AppenderCfg->AppenderName,
																 $AppenderConfigModel->GetLog4phpSettings($AppenderCfg->AppenderID));
		}
	}

	/**
	 * Associates all configured Appenders with the Root Logger.
	 *
	 * @return void.
	 */
	public function AssociateAppenders() {
		// TODO Add support for multiple loggers by extracting the associations from a configuration table in the Database

		foreach($this->LoggerConfig[LOGGER_LOG4PHP_APPENDERS] as $AppenderName => $AppenderSettings) {
			// Associate the Appenders to the Root Logger
			$this->LoggerConfig[LOGGER_LOG4PHP_ROOTLOGGER][LOGGER_LOG4PHP_APPENDERS][] = $AppenderName;
		}
	}

	/**
	 * Retrieves the configuration of all the Logger-level filters and stores it
	 * in the configuration array.
	 *
	 * @return void.
	 */
	protected function GetLoggerFilters() {
		// TODO Retrieve Logger-level Filters
		return '';
	}

	/**
	 * Processes the settings of all Logger parts and builds an array containing
	 * the complete configuration. This array is then saved in Vanilla
	 * configuration, so that loading it will simply involve reading it back.
	 *
	 * @return void.
	 */
	public function RebuildConfiguration() {
		$this->LoggerConfig = array(
													LOGGER_LOG4PHP_APPENDERS => array() // Section containing the list of all configured Appenders
													);

		// Initialize the arrays that will contain the configuration for Root Logger
		// TODO Allow to specify more than one Logger
		$this->InitializeLoggerSection(LOGGER_LOG4PHP_ROOTLOGGER);

		// Load setting of Appenders
		$this->LoadAppendersSettings();

		// Associate Appenders to the Loggers
		$this->AssociateAppenders();

		// Load filters to apply at a Logger level
		$this->GetLoggerFilters();

		// Save the configuration array to Vanilla's configuration
		SaveToConfig('Plugin.Logger.LoggerConfig', $this->LoggerConfig);
	}

	/**
	 * Returns an associative array containing the full configuration to be used
	 * by the Logger. Configuration should be initialised to a default as soon as
	 * the plugin is started. In case something went wrong, and no configuration
	 * is found, it returns the default configuration to prevent Log4php from
	 * triggering an exception.
	 *
	 * @return array An associative array containing the configuration for the
	 * Plugin.
	 *
	 */
	public function Get() {

		return C('Plugin.Logger.LoggerConfig', LoggerPlugin::$DefaultConfig);
	}
}
