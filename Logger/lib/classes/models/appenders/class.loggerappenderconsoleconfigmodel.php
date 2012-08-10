<?php if (!defined('APPLICATION')) exit();

class LoggerAppenderConsoleConfigModel extends LoggerAppenderConfigModel {

	public function GetAppenderConfig($AppenderID) {
		// Retrieve settings from configuration table
		$Config = parent::GetAppenderConfig($AppenderID);
		// Retrieve the settings encoded as JSON
		$AppenderParams = json_decode($Config['Configuration'], TRUE);

		$Config['Layout'] = $AppenderParams['layout']['class'];
		$Config['Target'] = $AppenderParams['params']['target'];

		return $Config;
	}

	public function Save($FormPostValues) {
		// Transforms posted values into an array to populate Configuration field in
		// LoggerAppenders configuration table
		$Config = array('layout' => array('class' => $FormPostValues['Layout']),
										'params' => array('target' => $FormPostValues['Target'],));

		$FormPostValues['Configuration'] = json_encode($Config);

		parent::Save($FormPostValues);
	}
}

// Install Model in Vanilla's factory so it can be instantiated by using its Alias
//Gdn::FactoryInstall('ApdConsoleConfigModel', 'LoggerConsoleAppenderConfigModel');
