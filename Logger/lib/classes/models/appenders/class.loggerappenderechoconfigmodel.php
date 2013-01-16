<?php if (!defined('APPLICATION')) exit();
/**
{licence}
*/

/**
 * Echo Appender Configuration Model
 * @package LoggerPlugin
 */
class LoggerAppenderEchoConfigModel extends LoggerAppenderConfigModel {
	/**
	 * @see DecodeAppenderParams::LoggerAppenderConfigModel()
	 */
	protected function DecodeAppenderParams(array &$AppenderConfig, array $AppenderParams) {
		$AppenderConfig['Layout'] = $AppenderParams['layout']['class'];
		$AppenderConfig['HtmlLineBreaks'] = $AppenderParams['params']['htmllinebreaks'];
	}

	/**
	 * @see DecodeAppenderParams::EncodeAppenderParams()
	 */
	protected function EncodeAppenderParams(array &$FormPostValues) {
		// Transforms posted values into an array to populate Configuration field in
		// LoggerAppenders configuration table
		$Config = array('layout' => array('class' => $FormPostValues['Layout']),
										'params' => array('htmllinebreaks' => $FormPostValues['HtmlLineBreaks'],));

		$FormPostValues['Configuration'] = json_encode($Config);
	}
}
