<?php	if (!defined('APPLICATION')) exit();
/**
{licence}
*/

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
		$this->AddRule('ValidAppenderClass', 'function:ValidateAppenderClass');
		$this->AddRule('ValidJSON', 'function:ValidateJSON');

		// Validation rules for Appender Type
		$this->ApplyRule('AppenderClass', 'ValidAppenderClass', T('Appender Type is not valid. Please select an Appender Type from the ones in the list.'));
		// Validation rules for JSON encoded parameters
		$this->ApplyRule('Configuration', 'ValidJSON', T('The value contained in Configuration field is not valid JSON. This field can only accept valid JSON, or NULL.'));
	}

	public function Validate($PostedFields, $Insert) {
		return parent::Validate($PostedFields, $Insert);
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
if(!function_exists('ValidateJSON')){
	/**
	 * Used when validating configuration settings for the Appenders. It checks if
	 * passed value is either either empty or a string represents valid JSON.
	 */
	function ValidateJSON($Value, $Field, $FormPostedValues){
		return (empty($Value) || is_object(json_decode($Value)));
	}
}
