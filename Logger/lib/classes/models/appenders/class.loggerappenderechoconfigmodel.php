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
		// Boolean values must be written in their string representation form for
		// Log4php to understand them correctly.
		$HtmlLineBreaks = ($AppenderParams['params']['htmllinebreaks'] == 'true') ? 1 : 0;

		$AppenderConfig['Layout'] = $AppenderParams['layout']['class'];
		$AppenderConfig['HtmlLineBreaks'] = $HtmlLineBreaks;
	}

	/**
	 * @see DecodeAppenderParams::EncodeAppenderParams()
	 */
	protected function EncodeAppenderParams(array &$FormPostValues) {
		// Boolean values must be written in their string representation form for
		// Log4php to understand them correctly.
		$HtmlLineBreaks = ($FormPostValues['HtmlLineBreaks'] == 1) ? 'true' : 'false';

		// Transforms posted values into an array to populate Configuration field in
		// LoggerAppenders configuration table
		$Config = array('layout' => array('class' => $FormPostValues['Layout']),
										'params' => array('htmllinebreaks' => $HtmlLineBreaks,));

		// If layout is LoggerLayoutPattern, save the pattern to be used
		if(GetValue('class', $Config['layout'], null) == 'LoggerLayoutPattern') {
			$Config['layout']['params'] = array('conversionPattern' => $FormPostValues['LayoutPattern']);
		}

		$FormPostValues['Configuration'] = json_encode($Config);
	}
}
