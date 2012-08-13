<?php if (!defined('APPLICATION')) exit();

class LoggerAppenderVanillaDBConfigModel extends LoggerAppenderConfigModel {

	public function GetAppenderConfig($AppenderID) {
		// Retrieve settings from configuration table
		$Config = parent::GetAppenderConfig($AppenderID);
		if(empty($Config)) {
			return false;
		}

		// Retrieve the settings encoded as JSON
		$AppenderParams = json_decode($Config['Configuration'], TRUE);

		$Config['Table'] = $AppenderParams['params']['table'];
		$Config['CreateTable'] = $AppenderParams['params']['createtable'];

		return $Config;
	}

	public function Save($FormPostValues) {
		// Transforms posted values into an array to populate Configuration field in
		// LoggerAppenders configuration table
		$Config = array('params' => array('table' => $FormPostValues['Table'],
																			'createtable' => $FormPostValues['CreateTable'],
																			));

		$FormPostValues['Configuration'] = json_encode($Config);

		return parent::Save($FormPostValues);
	}
}
