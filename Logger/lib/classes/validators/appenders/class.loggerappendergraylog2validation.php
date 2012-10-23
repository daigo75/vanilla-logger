<?php	if (!defined('APPLICATION')) exit();
/*
{licence}
*/

/**
 * It implements a set of rules used to validate the settings of a Log4php
 * Appender. It must be passed to any instance of LoggerAppenderConfigModel.
 */
class LoggerAppenderGraylog2Validation extends LoggerAppenderConfigValidation {
	/**
	 * Set Validation Rules that apply when saving the settings for a
	 * Graylog2 LoggerAppender.
	 */
	protected function SetValidationRules() {
		parent::SetValidationRules();

		// Validation rules for destination Table
		$this->ApplyRule('HostName', 'Required', T('Host Name is required.'));
		$this->ApplyRule('Port', 'Required', T('Port is required.'));
		$this->ApplyRule('Port', 'ValidateInteger', T('Port must be an Integer.'));
		$this->ApplyRule('ChunkSize', 'Required', T('Chunk Size is required.'));
		$this->ApplyRule('ChunkSize', 'ValidateInteger', T('Chunk Size must be an Integer.'));
	}
}

/**
 * Validation functions for Graylog2 Logger Appender.
 *
 */
