<?php if (!defined('APPLICATION')) exit();
/*
{licence}
*/

// Add LoggerAppender Info to a global array. It will be used to automatically
// add the Appender to the list of the available ones.
LoggerAppendersManager::$Appenders['LoggerAppenderConsole'] = array(
	'Label' => T('Console'),
	'Description' => T('Writes logging events to the <code>php://stdout</code> or the ' .
										 '<code>php://stderr</code> stream, the former being the default target.'),
);

// Class LoggerAppenderConsole is a standard Log4php class, therefore it
// doesn't have to be implemented.
