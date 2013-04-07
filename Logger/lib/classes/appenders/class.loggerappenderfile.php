<?php if (!defined('APPLICATION')) exit();
/**
{licence}
*/

// Register Appender with the Appenders Manager
LoggerAppendersManager::RegisterAppender(
	'LoggerAppenderFile',
	array('Label' => T('File'),
				'Description' => T('Writes logging events to a file.'),
				// Version is for reference only
				'Version' => '13.04.07',
			 )
);

// Class LoggerAppenderFile is a standard Log4php class, therefore it
// doesn't have to be implemented.
