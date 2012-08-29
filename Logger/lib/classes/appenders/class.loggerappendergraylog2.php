<?php

// TODO Implement Appender to GrayLog 2. Use Graylog2-gelf-php to communicate with it.
class LoggerAppenderGraylog2 extends LoggerAppender {
	// Log Table Model
	protected $LogModel;

	/// The properties below will be set automatically by Log4php with the data it
	/// will get from the configuration.


	public function __construct() {
		parent::__construct();
	}

	/**
	 * Transforms a Log4php Log Event into an associative array of fields, which
	 * will be saved to a database table.
	 *
	 * @param event A Log4php Event.
	 * @return An associative array of fields containing the information passed by
	 * the Log Event.
	 */
	protected function PrepareLogFields(LoggerLoggingEvent $event) {
		$Fields = array();

		$Fields['LoggerName'] = $event->getLoggerName();
		$Fields['Level'] = $event->getLevel()->toString();
		$Fields['Message'] = $event->getMessage();
		$Fields['Thread'] = $event->getThreadName();

		$LocationInformation = &$event->getLocationInformation();
		$Fields['ClassName'] = $LocationInformation->getClassName();
		$Fields['MethodName'] = $LocationInformation->getMethodName();
		$Fields['FileName'] = $LocationInformation->getFileName();
		$Fields['LineNumber'] = $LocationInformation->getLineNumber();
		$Fields['TimeStamp'] = date('Y-m-d H:i:s', $event->getTimeStamp());

		$ThrowableInfo = $event->getThrowableInformation();
		if(isset($ThrowableInfo)) {
			$Fields['Exception'] = $this->FormatThrowable($ThrowableInfo->getThrowable());
		}

		return $Fields;
	}

	/**
	 * Apply new configuration.
	 *
	 * @return True if configuration is applied successfully.
	 * @throws an Exception if configuration can't be applied successfully.
	 */
	public function activateOptions() {
		try {
			// Layout doesn't apply to this Logger, then use the default one
			$this->layout = new LoggerLayoutPattern();

			// Instantiate the Model that will send the log information to Graylog2
			// server
			$this->LogModel = new Graylog2Model();
		}
		catch (Exception $e) {
			throw new Exception($e);
		}
		return true;
	}

	/**
	 * Appends a new Log Entry to the Log Table.
	 *
	 * @param event A Log Event object, containing all Log Event Details.
	 * @return void.
	 */
	public function append(LoggerLoggingEvent $event) {
		$this->LogModel->Save($this->PrepareLogFields($event));
	}
}
