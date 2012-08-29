<?php

// Add LoggerAppender Info to a global array. It will be used to automatically
// add the Appender to the list of the available ones.
LoggerAppendersManager::$Appenders['LoggerAppenderGraylog2'] = array(
	'Label' => T('Graylog2 (GELF)'),
	'Description' => T('Writes logging events to a <a href="http://www.graylog2.org/">Graylog2</a> ' .
										 'Server, sending them in <a href="http://www.graylog2.org/about/gelf">GELF Format</a>'),

);

/**
 * Graylog2 Log Appender
 * @package LoggerPlugin
 */
class LoggerAppenderGraylog2 extends LoggerAppender {
	// Log Table Model
	protected $LogModel;

	/// The properties below will be set automatically by Log4php with the data it
	/// will get from the configuration.
	protected $HostName;
	protected $Port;
	protected $ChunkSize;

	public function getHostName() {
		return $this->HostName;
	}

	public function setHostName($Value) {
		$this->HostName = $Value;
	}

	public function getPort() {
		return $this->Port;
	}

	public function setPort($Value) {
		$this->Port = $Value;
	}

	public function getChunkSize() {
		return $this->ChunkSize;
	}

	public function setChunkSize($Value) {
		$this->ChunkSize = $Value;
	}

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Returns a string representation of an exception.
	 *
	 * @param Exception The exception to convert to a string.
	 * @return A string representation of the Exception.
	 */
	private function FormatThrowable(Exception $Exception) {
		return $Exception->__toString();
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
		$Fields['Level'] = $event->getLevel()->toInt();
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
			$this->LogModel = new Graylog2Model($this->HostName,
																					$this->Port,
																					$this->ChunkSize);
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
