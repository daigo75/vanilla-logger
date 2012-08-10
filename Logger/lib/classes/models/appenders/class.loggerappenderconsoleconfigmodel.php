<?php if (!defined('APPLICATION')) exit();

class LoggerAppenderConsoleConfigModel extends LoggerAppenderConfigModel {

	public function GetAppenderSettings($AppenderID) {
		// Retrieve settings XML from configuration table
		$Settings = &parent::GetAppenderSettings($AppenderID);
		$ConfigXML = $Settings['Configuration'];

		// TODO Transform XML settings, contained in Config field, into an array of fields
		$Config = json_decode(json_encode($ConfigXML));
		var_dump($Config);

		return $Settings;
	}

	public function Save($FormPostValues) {
		// TODO Transforms posted values into an array to populate Config field in Appenders configuration table
		$ConfigXML = new SimpleXMLElement('<appender/>');
		$this->AddLayoutNode($ConfigXML, $FormPostValues['Layout']);

		$this->AddParamNodeFromField($ConfigXML, $FormPostValues, 'Target');

		parent::Save($FormPostValues);
	}
}

// Install Model in Vanilla's factory so it can be instantiated by using its Alias
//Gdn::FactoryInstall('ApdConsoleConfigModel', 'LoggerConsoleAppenderConfigModel');
