<?php if (!defined('APPLICATION')) exit();
/**
{licence}
*/

/**
 * Vanilla DB Appender Configuration Model
 * @package LoggerPlugin
 */
class LoggerAppenderVanillaDBConfigModel extends LoggerAppenderConfigModel {
	/**
	 * @see DecodeAppenderParams::LoggerAppenderConfigModel()
	 */
	protected function DecodeAppenderParams(array &$AppenderConfig, array $AppenderParams) {
		$AppenderConfig['Table'] = $AppenderParams['params']['table'];
		$AppenderConfig['CreateTable'] = $AppenderParams['params']['createtable'];
	}

	/**
	 * @see DecodeAppenderParams::EncodeAppenderParams()
	 */
	protected function EncodeAppenderParams(array &$FormPostValues) {
		// Transforms posted values into an array to populate Configuration field in
		// LoggerAppenders configuration table
		$Config = array('params' => array('table' => $FormPostValues['Table'],
																			'createtable' => $FormPostValues['CreateTable'],
																			));

		$FormPostValues['Configuration'] = json_encode($Config);
	}
}
