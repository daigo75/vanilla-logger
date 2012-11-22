<?php if (!defined('APPLICATION')) exit();
/**
{licence}
*/

/**
 * Graylog2 Appender Configuration Model
 * @package LoggerPlugin
 */
class LoggerAppenderGraylog2ConfigModel extends LoggerAppenderConfigModel {
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
		$Config = array('params' => array('hostname' => $FormPostValues['HostName'],
																			'port' => $FormPostValues['Port'],
																			'chunksize' => $FormPostValues['ChunkSize'],
																			));

		$FormPostValues['Configuration'] = json_encode($Config);
	}
}
