<?php

// Add LoggerAppender Info to a global array. It will be used to automatically
// add the Appender to the list of the available ones.
LoggerAppendersManager::$Appenders['LoggerAppenderLoggly'] = array(
	'Label' => T('Loggly (HTTPS)'),
	'Description' => T('Writes logging events to <a href="http://www.loggly.com/">Loggly</a>, ' .
										 'using HTTPS.'),

);

/**
 * Loggly Log Appender
 * @package LoggerPlugin
 */
class LoggerAppenderLoggly extends LoggerAppender {
	// @var string The URL of Loggly Log Server
	protected $LogglyURL = 'https://logs.loggly.com/inputs';

	// The properties below will be set automatically by Log4php with the data it
	// will get from the configuration.

	// @var string The SHA Input Key to be used to send Logs to Loggly via HTTPS
	protected $InputKey;

	/**
	 * Setter for InputKey field.
	 */
	public function setInputKey($Value) {
		$this->InputKey = $Value;
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
	 * Builds and returns the full URL where the Log messages will be sent.
	 *
	 * @return string The full URL where the Log messages will be sent.
	 */
	protected function GetLoggerURL() {
		return sprintf('%s/%s',
									 $this->LogglyURL,
									 $this->InputKey);
	}

	/**
	 * Builds a JSON Message that will be sent to a Loggly Server.
	 *
	 * @param LoggerLoggingEvent event A Log4php Event.
	 * @return string A JSON structure representing the message.
	 */
	protected function BuildJSONMessage(LoggerLoggingEvent $event) {
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

		return json_encode($Fields);
	}

	/**
	 * Sends a JSON Message to Loggly.
	 *
	 * @param string Message The JSON-Encoded Message to be sent.
	 * @return bool True if message was sent correctly, False otherwise.
	 */
	protected function PublishMessage($Message) {
		$ch = curl_init($this->GetLoggerURL());

		try {
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: Application/json'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_CAINFO, LOGGER_PLUGIN_CERTS_PATH . '/cacert.pem');
			//curl_setopt($ch, CURLOPT_HEADER, 0);  // No HTTP Headers

			curl_setopt($ch, CURLOPT_POSTFIELDS, $Message);
			//var_dump(curl_error($ch));

			$Result = curl_exec($ch);
			//var_dump(curl_error($ch));
			//$Info = curl_getinfo($ch);
			//var_dump($Info);
		}
		catch(Exception $e) {
			print(curl_error($ch));
			trigger_error(sprtinf('Exception occurred while posting the message to loggly. Last CURL Error: %s. Exception details: %s',
														curl_error($ch),
														$e->__toString()));
		}
		curl_close($ch);

		return true;
	}

	/**
	 * Apply new configuration.
	 *
	 * @return bool True if configuration is applied successfully.
	 * @throws An Exception if configuration can't be applied successfully.
	 */
	public function activateOptions() {
		try {
			// Layout doesn't apply to this Logger, then use the default one
			$this->layout = new LoggerLayoutSimple();
		}
		catch (Exception $e) {
			throw new Exception($e);
		}
		return true;
	}

	/**
	 * Appends a new Log Entry to the Log Table.
	 *
	 * @param LoggerLoggingEvent event A Log Event object, containing all Log Event Details.
	 * @return bool True if message was saved correctly, False otherwise.
	 */
	public function append(LoggerLoggingEvent $event) {
		$Message = $this->BuildJSONMessage($event);

		return $this->PublishMessage($Message);
	}
}
