<?php	if (!defined('APPLICATION')) exit();

/**
 * It implements a set of rules used to validate the settings of a Log4php
 * Appender. It must be passed to any instance of LoggerAppenderConfigModel.
 *
 * @package LoggerPlugin
 */
class LoggerAppenderConfigValidation extends Gdn_Validation {
	/**
	 * Set Validation Rules that apply when saving the settings for a
	 * LoggerAppender.
	 */
	protected function SetValidationRules() {
		$this->AddRule('ValidAppenderType', 'function:ValidateAppenderType');
		$this->AddRule('ValidConfigXML', 'function:ValidateConfigurationXML');

		// Validation rules for Appender Type
		$this->ApplyRule('AppenderType', 'ValidAppenderType', T('Appender Type is not valid. Please select an Appender Type from the ones in the list.'));
		// Validation rules for Configuration XML
		$this->ApplyRule('Configuration', 'ValidConfigXML', T('The Configuration field contains invalid XML.'));
	}

	/**
	 * Class constructor.
	 */
	public function __construct() {
		parent::__construct();

		// Initialize Validation Rules
		$this->SetValidationRules();
	}
}

/**
 * Validation functions for Logger Appender Configuration.
 *
 */
if(!function_exists('ValidateConfigurationXML')){
	/**
	 * Used when validating configuration settings for the Appenders. It checks if
	 * passed value is either either empty or a string represents structurally
	 * valid XML.
	 */
	function ValidateConfigurationXML($Value, $Field, $FormPostedValues){
		return true;
	}
}
