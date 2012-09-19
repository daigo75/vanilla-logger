<?php

// Add LoggerAppender Info to a global array. It will be used to automatically
// add the Appender to the list of the available ones.
LoggerAppendersManager::$Appenders['LoggerAppenderLogglySyslog'] = array(
	'Label' => T('Loggly - JSON via Remote SysLog'),
	'Description' => T('Sends log events to <a href="http://www.loggly.com/">Loggly</a>, in JSON format, ' .
										 'using Remote Syslog (UDP). <br />If you\'d like to send events to Loggly as plain ' .
										 'text, please use the standard Remote Syslog appender).'));

/**
 * Loggly Log Appender
 * @package LoggerPlugin
 */
class LoggerAppenderLogglySyslog extends LoggerAppenderRSyslog {
	// @var int Default UDP Port for JSON Remote Syslog on Loggly
	const DEFAULT_PORT = 42146;

	// The properties below will be set automatically by Log4php with the data it
	// will get from the configuration.

	/**
	 * Transforms a Log4ph event into an associative array.
	 *
	 * @param LoggerLoggingEvent event A Log4php Event.
	 * @return array An associative array representation of the event.
	 */
	protected function EventToArray(LoggerLoggingEvent $event) {
		$Fields = array();

		$Fields['LoggerName'] = $event->getLoggerName();
		$Fields['Level'] = $event->getLevel()->getSysLogEquivalent();
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
	 * Builds a Message that will be sent to a RSyslog Server.
	 *
	 * @param LoggerLoggingEvent event A Log4php Event.
	 * @return string A string representing the message.
	 */
	protected function BuildSysLogMessage(LoggerLoggingEvent $event) {
		return new LogglySyslogMessage($this->EventToArray($event),
																	 self::DEFAULT_FACILITY,
																	 $event->getLevel()->getSysLogEquivalent(),
																	 $event->getTimeStamp());
	}
}

/**
 * Implementation of Remote Syslog Message for Loggly. This class logs the
 * events using JSON, which allows providing more details than basic Syslog.
 *
 * @see SyslogMessage.
 */
class LogglySyslogMessage extends SyslogMessage {
	public function __construct($Message, $Facility = 16, $Severity = 5, $Timestamp, $Options = null) {
		parent::__construct($Message, $Facility, $Severity, $Timestamp, $Options);
	}

	/**
	 * Puts all Log Message elements together to form a JSON String that will be
	 * passed to the RSysLog Server.
	 *
	 * @return string The Message as a JSON object.
	 */
	protected function FormatMessage() {
		$this->Message['FQDN'] = $this->GetFQDN();
		$this->Message['ProcessName'] = $this->GetProcessName();
		$this->Message['PID'] = getmypid();

		return json_encode($this->Message);
	}

	/**
	 * Returns the chunks of the message to send to the RSysLog server.
	 * Note: this specific implementation sends messages as whole JSON Objects,
	 * there are no "chunks".
	 *
	 * @return string A JSON representation of the Log message.
	 */
	public function GetMessageChunks() {
		return(array($this->FormatMessage()));
	}

}
