<?php

// TODO Implement Appender to GrayLog 2. Use Graylog2-gelf-php to communicate with it.
class LoggerAppenderGraylog2 extends LoggerAppender {

	public function __construct() {
		parent::__construct();

		die('Feck off');
	}

	protected function append(LoggerLoggingEvent $event) {
	}
}
