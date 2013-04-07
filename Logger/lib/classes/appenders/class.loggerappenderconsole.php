<?php if (!defined('APPLICATION')) exit();
/**
{licence}
*/

// Register Appender with the Appenders Manager
LoggerAppendersManager::RegisterAppender(
	'LoggerAppenderConsole',
	array('Label' => T('Console'),
				'Description' => T('Writes logging events to the <code>php://stdout</code> or the ' .
										 '<code>php://stderr</code> stream, the former being the default target'),
				// Version is for reference only
				'Version' => '13.04.07',
				)
);

// Class LoggerAppenderConsole is a standard Log4php class, therefore it
// doesn't have to be implemented.
