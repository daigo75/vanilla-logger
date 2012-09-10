<?php

// Add LoggerAppender Info to a global array. It will be used to automatically
// add the Appender to the list of the available ones.
LoggerAppendersManager::$Appenders['LoggerAppenderRSyslog'] = array(
	'Label' => T('RSyslog (Remote SysLog)'),
	'Description' => T('Writes logging events to Remote Syslog Server, such as <a href="http://www.papertrail.com/">PaperTrail</a>.'),

);

/**
 * RSyslog Log Appender
 * @package LoggerPlugin
 */
class LoggerAppenderRSyslog extends LoggerAppender {
	// Log Table Model
	protected $LogModel;

	// The properties below will be set automatically by Log4php with the data it
	// will get from the configuration.

	// @var string The address of the RSyslog log to which the log messages will be sent.
	protected $HostName;
	// @var int The port to use to connect to RSyslog server.
	protected $Port;
	// @var int Timeout tro be used when communicating with Remote SysLog Server
	protected $Timeout;

	public function getTimeout() {
		return $this->Timeout;
	}

	public function setTimeout($Value) {
		$this->Timeout = $Value;
	}

	public function getPort() {
		return $this->Port;
	}

	public function setPort($Value) {
		$this->Port = $Value;
	}

	public function getHostName() {
		return $this->HostName;
	}

	public function setHostName($Value) {
		$this->HostName = $Value;
	}

	public function __construct($name = '') {
		parent::__construct($name);
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

		// TODO Find a way to log Exception details, which seem to be ignored by all Layout formatters.
		$Fields['Message'] = $this->layout->format($event);
		$Fields['Severity'] = $event->getLevel()->getSysLogEquivalent();
		$Fields['Facility'] = RSyslogModel::DEFAULT_FACILITY;
		$Fields['TimeStamp'] = $event->getTimeStamp();

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
			// Use Log4php default layout to format the Log Message
			// TODO Make Layout configurable.
			$this->layout = new LoggerLayoutPattern();
			$this->layout->setConversionPattern('%c - %m %x. Location: %l.');

			// Instantiate the Model that will send the log information to Graylog2
			// server
			$this->LogModel = new RSyslogModel($this->HostName, $this->Port);
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
