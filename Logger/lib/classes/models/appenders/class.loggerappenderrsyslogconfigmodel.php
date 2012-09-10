<?php if (!defined('APPLICATION')) exit();

/**
 * Loggly Appender Configuration Model
 * @package LoggerPlugin
 */
class LoggerAppenderRSyslogConfigModel extends LoggerAppenderConfigModel {
	/**
	 * @see DecodeAppenderParams::LoggerAppenderConfigModel()
	 */
	protected function DecodeAppenderParams(array &$AppenderConfig, array $AppenderParams) {
		$AppenderConfig['HostName'] = $AppenderParams['params']['hostname'];
		$AppenderConfig['Port'] = $AppenderParams['params']['port'];
		$AppenderConfig['ChunkSize'] = $AppenderParams['params']['chunksize'];
	}

	/**
	 * @see DecodeAppenderParams::EncodeAppenderParams()
	 */
	protected function EncodeAppenderParams(array &$FormPostValues) {
		// Transforms posted values into an array to populate Configuration field in
		// LoggerAppenders configuration table
		$Config = array('params' => array('hostname' => $FormPostValues['HostName'],
																			'port' => $FormPostValues['Port'],
																			'timeout' => $FormPostValues['Timeout'],
																			));
		$FormPostValues['Configuration'] = json_encode($Config);
	}
}
