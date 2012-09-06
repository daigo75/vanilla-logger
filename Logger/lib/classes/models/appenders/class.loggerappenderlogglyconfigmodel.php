<?php if (!defined('APPLICATION')) exit();

/**
 * Loggly Appender Configuration Model
 * @package LoggerPlugin
 */
class LoggerAppenderLogglyConfigModel extends LoggerAppenderConfigModel {
	/**
	 * @see DecodeAppenderParams::LoggerAppenderConfigModel()
	 */
	protected function DecodeAppenderSpecificParams(array &$AppenderConfig, array $AppenderParams = null) {
		$Config['InputKey'] = $AppenderParams['params']['inputkey'];

		return $Config;
	}

	/**
	 * @see DecodeAppenderParams::EncodeAppenderParams()
	 */
	protected function EncodeAppenderParams(array &$FormPostValues) {
		// Transforms posted values into an array to populate Configuration field in
		// LoggerAppenders configuration table
		$Config = array('params' => array('inputkey' => $FormPostValues['InputKey'],
																			));

		$FormPostValues['Configuration'] = json_encode($Config);
	}
}
