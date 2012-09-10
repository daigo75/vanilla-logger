<?php if (!defined('APPLICATION')) exit();

/**
 * Console Appender Configuration Model
 * @package LoggerPlugin
 */
class LoggerAppenderConsoleConfigModel extends LoggerAppenderConfigModel {
	/**
	 * @see DecodeAppenderParams::LoggerAppenderConfigModel()
	 */
	protected function DecodeAppenderParams(array &$AppenderConfig, array $AppenderParams = null) {
		$AppenderConfig['Layout'] = $AppenderParams['layout']['class'];
		$AppenderConfig['Target'] = $AppenderParams['params']['target'];
	}

	/**
	 * @see DecodeAppenderParams::EncodeAppenderParams()
	 */
	protected function EncodeAppenderParams(array &$FormPostValues) {
		// Transforms posted values into an array to populate Configuration field in
		// LoggerAppenders configuration table
		$Config = array('layout' => array('class' => $FormPostValues['Layout']),
										'params' => array('target' => $FormPostValues['Target'],));

		$FormPostValues['Configuration'] = json_encode($Config);
	}
}
