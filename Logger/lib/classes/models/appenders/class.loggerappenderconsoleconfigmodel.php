<?php if (!defined('APPLICATION')) exit();
/**
{licence}
*/

/**
 * Console Appender Configuration Model
 * @package LoggerPlugin
 */
class LoggerAppenderConsoleConfigModel extends LoggerAppenderConfigModel {
	/**
	 * @see DecodeAppenderParams::LoggerAppenderConfigModel()
	 */
	protected function DecodeAppenderParams(array &$AppenderConfig, array $AppenderParams) {
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

		// If layout is LoggerLayoutPattern, save the pattern to be used
		if(GetValue('class', $Config['layout'], null) == 'LoggerLayoutPattern') {
			$Config['layout']['params'] = array('conversionPattern' => $FormPostValues['LayoutPattern']);
		}

		$FormPostValues['Configuration'] = json_encode($Config);
	}
}
