<?php	if (!defined('APPLICATION')) exit();

/**
 * Implements a set of rules used to validate the settings of a Rsyslog Appender.
 */
class LoggerAppenderRsyslogValidation extends LoggerAppenderConfigValidation {
	/**
	 * Set Validation Rules that apply when saving the settings for a
	 * Rsyslog LoggerAppender.
	 */
	protected function SetValidationRules() {
		parent::SetValidationRules();
		$this->AddRule('PositiveInteger', 'function:ValidatePositiveInteger');
		$this->AddRule('ValidPort', 'function:ValidateTCPPort');

		// Validation rules for destination Table
		$this->ApplyRule('HostName', 'Required', T('Host Name is required.'));
		$this->ApplyRule('Port', 'Required', T('Port is required.'));
		$this->ApplyRule('Port', 'ValidPort', T('Port must be an Integer and have a value in the range 1-65535.'));
		$this->ApplyRule('Timeout', 'Required', T('Timeout is required.'));
		$this->ApplyRule('Timeout', 'PositiveInteger', T('Timeout must be a positive Integer.'));
	}
}

/**
 * Validation functions for Rsyslog Logger Appender.
 *
 */
