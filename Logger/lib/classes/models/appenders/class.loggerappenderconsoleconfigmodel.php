<?php if (!defined('APPLICATION')) exit();

class LoggerAppenderConsoleConfigModel extends LoggerAppendersModel {

	public function GetAppenderSettings($AppenderID) {
		// Retrieve raw settings from configuration table
		$RawSettings = &parent::GetAppenderSettings($AppenderID);

		// TODO Transform raw settings, contained in Config field, into an array of fields
	}

	public function Save(&$FormPostValues) {
		// TODO Transforms posted values into an array to populate Config field in Appenders configuration table
	}
}

// Install Model in Vanilla's factory so it can be instantiated by using its Alias
//Gdn::FactoryInstall('ApdConsoleConfigModel', 'LoggerConsoleAppenderConfigModel');
