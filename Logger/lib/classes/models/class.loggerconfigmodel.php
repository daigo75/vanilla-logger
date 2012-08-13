<?php if (!defined('APPLICATION')) exit();

/**
 * Logger Appenders Model
 *
 * @package LoggerPlugin
 */

/**
 * This model is used to store Log entries to a table.
  */
class LoggerConfigModel extends Gdn_Model {
	/**
	 * Defines the related database table name.
	 *
	 */
	public function __construct() {
		parent::__construct('LoggerConfig');
	}

	public function LoadAppendersConfig(array &$LoggerConfig) {
		// TODO Retrieve configuration for each Appender
		$AppenderCfgModel = new LoggerAppenderConfigModel();
		$ActiveAppenders = $AppenderCfgModel->GetActiveAppenders();

		foreach($ActiveAppenders as $AppenderCfg) {
			var_dump($AppenderCfg->AppenderType);;
		}

		$LoggerConfig['appenders'] = '';
	}

	public function GetLoggerFilters() {
		// TODO Retrieve Logger-level Filters
		return '';
	}

	/**
	 * Returns an associative array containing the full configuraiton to be used
	 * by the Logger.
	 *
	 */
	public function Get() {
		$LoggerConfig = array();

		// TODO Allow to specify more than one Logger
		// LoggerPlugin will use a single logger
		$LoggerConfig['rootLogger'] = array();
		//$LoggerConfig['rootLogger']['appenders'] = $this->GetAppendersForLogger('rootLogger');

		// Load setting of Appenders
		$this->LoadAppendersConfig($LoggerConfig);


		$this->GetLoggerFilters();

		$LoggerConfig = '';

		return $LoggerConfig;
	}

	/**
	 * Returns an array containing a single row with the settings for a specific
	 * Logger Appender.
	 *
	 * @param AppenderID The Id of the Appender for which the data should be
	 * retrieved.
	 * @return An array containing a single row with the settings for the Logger
	 * Appender, or FALSE if no result is found.
	 */
	protected function GetLoggerConfig($LoggerName) {
		// Appender ID must be a number, therefore there's no point in running a
		// query if it's empty or non-numeric.
		if(empty($AppenderID) || !is_numeric($AppenderID)) {
			return null;
		}

		// Retrieve and return the Appender Settings
		return $this->Get(array('AppenderID' => $AppenderID,))->FirstRow(DATASET_TYPE_ARRAY);
	}
}
