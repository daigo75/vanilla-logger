<?php	if (!defined('APPLICATION')) exit();

/**
 * It implements a set of rules used to validate the settings of a Log4php
 * Appender. It must be passed to any instance of LoggerAppenderConfigModel.
 */
class LoggerAppenderConsoleValidation extends LoggerAppenderConfigValidation {
	/**
	 * Set Validation Rules that apply when saving the settings for a
	 * LoggerAppender.
	 */
	protected function SetValidationRules() {
		parent::SetValidationRules();

		$this->AddRule('ValidTarget', 'function:ValidateTarget');

		// Validation rules for Target
		$this->ApplyRule('Target', 'ValidateTarget', T('Target is not valid. Valid values are <code>stdout</code> and <code>stderr</code>.'));
	}
}

/**
 * Validation functions for Console Logger Appender.
 *
 */
// TODO Export all these functions to an independent plugin, to allow other plugins to use them.
if(!function_exists('ValidateTarget')){
	/**
	 * Checks if an Appender Class is amongst the available ones.
	 */
	function ValidateTarget($Value, $Field, $FormPostedValues){
		return in_array($Value, array('stdout', 'stderr'));
	}
}
