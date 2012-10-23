<?php	if (!defined('APPLICATION')) exit();
/*
{licence}
*/

/**
 * Implements a set of rules used to validate the settings of a Loggly Appender.
 */
class LoggerAppenderLogglyValidation extends LoggerAppenderConfigValidation {
	/**
	 * Set Validation Rules that apply when saving the settings for a
	 * Loggly LoggerAppender.
	 */
	protected function SetValidationRules() {
		parent::SetValidationRules();

		// Validation rules for destination Table
		$this->ApplyRule('InputKey', 'Required', T('Input Key is required.'));
	}
}

/**
 * Validation functions for Loggly Logger Appender.
 *
 */
