<?php if (!defined('APPLICATION')) exit();

class LoggerAppenderConsoleConfigModel extends LoggerAppenderConfigModel {

	public function GetAppenderSettings($AppenderID) {
		// Retrieve settings XML from configuration table
		$SettingsXML = &parent::GetAppenderSettings($AppenderID);

		// TODO Transform XML settings, contained in Config field, into an array of fields
		$Settings = json_decode(json_encode($SettingsXML));
		var_dump($Settings);
		die();
	}

	public function Save(&$FormPostValues) {
		// TODO Transforms posted values into an array to populate Config field in Appenders configuration table
	}
}

// Install Model in Vanilla's factory so it can be instantiated by using its Alias
//Gdn::FactoryInstall('ApdConsoleConfigModel', 'LoggerConsoleAppenderConfigModel');
