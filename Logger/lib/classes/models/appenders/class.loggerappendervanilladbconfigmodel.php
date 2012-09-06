<?php if (!defined('APPLICATION')) exit();

/**
 * Vanilla DB Appender Configuration Model
 * @package LoggerPlugin
 */
class LoggerAppenderVanillaDBConfigModel extends LoggerAppenderConfigModel {
	/**
	 * Expands the data contained in the Configuration field of an Appender
	 * Configuration in the fields that form it.
	 *
	 * @param string AppenderID The ID of the Appender for which to retrieve the
	 * configuration.
	 * @return array An associative array of Configuration parameters\.
	 */
	public function GetAppenderConfig($AppenderID) {
		// Retrieve settings from configuration table
		$Config = parent::GetAppenderConfig($AppenderID);
		if(empty($Config)) {
			return false;
		}

		// Retrieve the settings encoded as JSON
		$AppenderParams = json_decode($Config['Configuration'], TRUE);

		$Config['Table'] = $AppenderParams['params']['table'];
		$Config['CreateTable'] = $AppenderParams['params']['createtable'];

		return $Config;
	}

	/**
	 * Transforms a set of posted fields into a JSON structure to be saved in
	 * Appenders configuration table and saves them.
	 *
	 * @param array FormPostValues An associative array of values posted by a form.
	 * @return bool True if the values could be saved correctly, False otherwise\.
	 */
	public function Save($FormPostValues) {
		// Transforms posted values into an array to populate Configuration field in
		// LoggerAppenders configuration table
		$Config = array('params' => array('table' => $FormPostValues['Table'],
																			'createtable' => $FormPostValues['CreateTable'],
																			));

		$FormPostValues['Configuration'] = json_encode($Config);

		return parent::Save($FormPostValues);
	}
}
