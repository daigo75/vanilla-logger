<?php if (!defined('APPLICATION')) exit();
/**
{licence}
*/

// Add LoggerAppender Info to a global array. It will be used to automatically
// add the Appender to the list of the available ones.
LoggerAppendersManager::$Appenders['LoggerAppenderFile'] = array(
	'Label' => T('File'),
	'Description' => T('Writes logging events to a file.'),
);

// Class LoggerAppenderFile is a standard Log4php class, therefore it
// doesn't have to be implemented.
