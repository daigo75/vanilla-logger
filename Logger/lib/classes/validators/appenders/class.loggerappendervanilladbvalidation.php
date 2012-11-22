<?php	if (!defined('APPLICATION')) exit();
/**
{licence}
*/

/**
 * It implements a set of rules used to validate the settings of a Log4php
 * Appender. It must be passed to any instance of LoggerAppenderConfigModel.
 */
class LoggerAppenderVanillaDBValidation extends LoggerAppenderConfigValidation {
	/**
	 * Set Validation Rules that apply when saving the settings for a
	 * VanillaDB LoggerAppender.
	 */
	protected function SetValidationRules() {
		parent::SetValidationRules();

		// Validation rules for destination Table
		$this->ApplyRule('Table', 'Required', T('Table is required.'));
	}
}

/**
 * Validation functions for VanillaDB Logger Appender.
 *
 */
