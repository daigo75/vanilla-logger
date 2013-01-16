<?php	if (!defined('APPLICATION')) exit();
/**
{licence}
*/

/**
 * It implements a set of rules used to validate the settings of a Log4php
 * Appender. It must be passed to any instance of LoggerAppenderConfigModel.
 */
class LoggerAppenderFileValidation extends LoggerAppenderConfigValidation {
	/**
	 * Set Validation Rules that apply when saving the settings for a
	 * LoggerAppender.
	 */
	protected function SetValidationRules() {
		parent::SetValidationRules();

		$this->AddRule('FileIsWritable', 'function:ValidateFileIsWritable');

		// Validation rules for Target
		$this->ApplyRule('File',
										 'FileIsWritable',
										 T('Specified file is not valid, or it\'s not writable. ' .
											 'Please make sure that the path exists and that it is writable.'));
	}
}

/**
 * Validation functions for File Logger Appender.
 *
 */
// TODO Export all these functions to an independent plugin, to allow other plugins to use them.
if(!function_exists('ValidateFileIsWritable')){
	/**
	 * Checks if an a specified file is writable
	 */
	function ValidateFileIsWritable($Value, $Field, $FormPostedValues){
		if(empty($Value)) {
			return false;
		}
		$DestinationDir = realpath(pathinfo($Value, PATHINFO_DIRNAME));
		return is_writable($DestinationDir);
	}
}
