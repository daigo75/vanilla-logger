<?php if (!defined('APPLICATION')) exit();
/**
{licence}
*/

/**
 * Loggly Appender Configuration Model
 * @package LoggerPlugin
 */
class LoggerAppenderLogglyConfigModel extends LoggerAppenderConfigModel {
	/**
	 * @see DecodeAppenderParams::LoggerAppenderConfigModel()
	 */
	protected function DecodeAppenderParams(array &$AppenderConfig, array $AppenderParams) {
		$AppenderConfig['InputKey'] = $AppenderParams['params']['inputkey'];
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
