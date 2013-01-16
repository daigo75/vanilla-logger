<?php if (!defined('APPLICATION')) exit();
/**
{licence}
*/

/**
 * File Appender Configuration Model
 * @package LoggerPlugin
 */
class LoggerAppenderFileConfigModel extends LoggerAppenderConfigModel {
	/**
	 * @see DecodeAppenderParams::LoggerAppenderConfigModel()
	 */
	protected function DecodeAppenderParams(array &$AppenderConfig, array $AppenderParams) {
		$AppenderConfig['AppendToFile'] = $this->BoolStrToInt($AppenderParams['params']['append']);
		$AppenderConfig['File'] = $AppenderParams['params']['file'];

		$AppenderConfig['Layout'] = GetValue('class', $AppenderParams['layout'], LoggerAppenderConfigModel::DEFAULT_LAYOUT);
		if(isset($Config['layout']['params'])) {
			$AppenderConfig['LayoutPattern'] = GetValue('conversionPattern', $Config['layout']['params'], LoggerAppenderConfigModel::DEFAULT_LAYOUT_PATTERN);
		}
		else {
			$AppenderConfig['LayoutPattern'] = LoggerAppenderConfigModel::DEFAULT_LAYOUT_PATTERN;
		}
	}

	/**
	 * @see DecodeAppenderParams::EncodeAppenderParams()
	 */
	protected function EncodeAppenderParams(array &$FormPostValues) {
		// Transforms posted values into an array to populate Configuration field in
		// LoggerAppenders configuration table
		$Config = array('layout' => array('class' => $FormPostValues['Layout']),
										'params' => array('file' => $FormPostValues['File'],
																			'append' => $this->IntToBoolStr($FormPostValues['AppendToFile'])
																			));

		// If layout is LoggerLayoutPattern, save the pattern to be used
		if(GetValue('class', $Config['layout'], null) == 'LoggerLayoutPattern') {
			$Config['layout']['params'] = array('conversionPattern' => $FormPostValues['LayoutPattern']);
		}

		$FormPostValues['Configuration'] = json_encode($Config);
	}
}
