<?php if (!defined('APPLICATION')) exit();
/**
{licence}
*/

// Register Appender with the Appenders Manager
LoggerAppendersManager::RegisterAppender(
	'LoggerAppenderEcho',
	array('Label' => T('Echo'),
				'Description' => T('Writes logging events using PHP\'s <code>echo()</code> function. ' .
													 'Echo outputs may be buffered.'),
				// Version is for reference only
				'Version' => '13.04.07',
			 )
);

// Class LoggerAppenderEcho is a standard Log4php class, therefore it
// doesn't have to be implemented.
