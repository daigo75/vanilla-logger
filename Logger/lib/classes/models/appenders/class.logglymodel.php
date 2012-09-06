<?php if (!defined('APPLICATION')) exit();
/**
 * Loggly Log Model
 *
 * @package LoggerPlugin
 */
class LogglyModel extends Gdn_Model {
	// @var string The URL of Loggly Log Server
	protected $LogglyURL = 'https://logs.loggly.com/inputs/';
	// @var string The SHA Input Key to be used to send Logs to Loggly via HTTPS
	protected $InputKey;

	/**
	 * Set Validation Rules that apply when saving a new row in Cron Jobs History.
	 *
	 * @return void
	 */
	protected function _SetLogglyValidationRules() {
		//$this->Validation = &new Gdn_Validation();

		// Set additional Validation Rules here. Please note that formal validation
		// is done automatically by base Model Class, by retrieving Schema
		// Information.
	}

	/**
	 * Builds and returns the full URL where the Log messages will be sent.
	 *
	 * @return string The full URL where the Log messages will be sent.
	 */
	protected function GetLogURL() {
		return sprintf('%s/%s',
									 $this->LogglyURL,
									 $this->InputKey);
	}

	/**
	 * Defines the related database table name. Table name must be passed as a
	 * parameter.
	 *
	 * @param string The SHA Input Key to be used to send Logs to Loggly via HTTPS.
	 */
	public function __construct($InputKey) {
		parent::__construct();

		$this->InputKey = $HostName;

		$this->_SetLogglyValidationRules();
	}

	/**
	 * Builds a JSON Message that will be sent to a Loggly Server.
	 *
	 * @param array LogFields An associative array of fields describing a Log Entry.
	 * @return string A JSON structure representing the message.
	 */
	protected function BuildJSONMessage(array &$LogFields) {
		return json_encode($LogFields);
		//$Message = new GELFMessage();
		//
		//$Message->setAdditional('LoggerName', $LogFields['LoggerName']);
		//// Level is passed as a SysLog level. The translation between Log4php and
		//// Syslog level is done by the caller.
		//$Message->setLevel($LogFields['Level']);
		//$Message->setShortMessage($LogFields['Message']);
		//$Message->setAdditional('Thread', $LogFields['Thread']);
		//$Message->setAdditional('ClassName', $LogFields['ClassName']);
		//$Message->setAdditional('MethodName', $LogFields['MethodName']);
		//$Message->setFile($LogFields['FileName']);
		//$Message->setLine($LogFields['LineNumber']);
		//$Message->setTimestamp($LogFields['TimeStamp']);
		//
		//$Message->setFullMessage($LogFields['Exception']);
		//
		//return $Message;
	}

	/**
	 * Sends a JSON Message to Loggly.
	 *
	 * @param string Message The JSON-Encoded Message to be sent.
	 * @return bool True if message was sent correctly, False otherwise.
	 */
	protected function PublishMessage($Message) {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->GetLogURL());
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // Follow redirects
		curl_setopt($ch, CURLOPT_HEADER, 0);  // No HTTP Headers

		curl_setopt($ch, CURLOPT_POSTFIELDS, $Message);

		$Result = curl_exec($ch);
		//$Info = curl_getinfo($ch);
		curl_close($ch);
	}

	/**
	 * Save a Log Entry to Loggly Server.
	 *
   * @param array LogFields An associative array of Log Entry fields.
   * @return bool True if the message was sent correctly, False otherwise.
	 */
	public function Save(&$LogFields) {
		// Validate posted data
		//if(!$this->Validate($LogFields)) {
		//	return false;
		//}

		$Message = $this->BuildJSONMessage($LogFields);

		try {
			return $this->PublishMessage($Message);
		}
		catch(Exception $e) {
			trigger_error(sprintf('log4php: Exception occurred while sending message to Loggly. Details:',
														$e->__toString()));
			return false;
		}
	}
}
