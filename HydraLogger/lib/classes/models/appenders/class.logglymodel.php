<?php if (!defined('APPLICATION')) exit();
/**
 * Loggly Log Model
 *
 * @package LoggerPlugin
 */
class LogglyModel extends Gdn_Model {
	// @var string The URL of Loggly Log Server
	protected $LogglyURL = 'https://logs.loggly.com/inputs';
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

		$this->InputKey = $InputKey;

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
	}

	/**
	 * Sends a JSON Message to Loggly.
	 *
	 * @param string Message The JSON-Encoded Message to be sent.
	 * @return bool True if message was sent correctly, False otherwise.
	 */
	protected function PublishMessage($Message) {
		$ch = curl_init($this->GetLogURL());

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

		return $this->PublishMessage($Message);
	}
}
