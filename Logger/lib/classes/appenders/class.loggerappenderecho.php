<?php if (!defined('APPLICATION')) exit();
/**
{licence}
*/

// Add LoggerAppender Info to a global array. It will be used to automatically
// add the Appender to the list of the available ones.
LoggerAppendersManager::$Appenders['LoggerAppenderEcho'] = array(
	'Label' => T('Echo'),
	'Description' => T('Writes logging events using PHP\'s <code>echo()</code> function. ' .
										 'Echo outputs may be buffered.'),
);

// Class LoggerAppenderEcho is a standard Log4php class, therefore it
// doesn't have to be implemented.
