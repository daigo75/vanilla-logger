<?php if (!defined('APPLICATION')) exit();

// Load RSysLog Libraries
require(LOGGER_PLUGIN_EXTERNAL_PATH . '/rsyslog/rsyslog.php');

/**
 * RSyslog Log Model
 *
 * @package LoggerPlugin
 */
class RSyslogModel extends Gdn_Model {
	// @var string The address of Remote Syslog Log Server
	protected $HostName;
	// @var int The port to use to connect to Remote Syslog server.
	protected $Port;
	// @var RSysLog An instance of RSyslog class, used to send messages to the Rremote Syslog server/
	private $SyslogPublisher;

	// @var int The default Timeout to be used when communicating with the Remote Syslog Server/
	const DEFAULT_TIMEOUT = 1;

	// TODO Allow User to choose the Facility from one of the values provided by SyslogFacility Class.
	// @var int The default Facility used to build Syslog Messages.
	const DEFAULT_FACILITY = SyslogFacility::USER;

	/**
	 * Set Validation Rules that apply when saving a new row in Cron Jobs History.
	 *
	 * @return void
	 */
	protected function _SetRSyslogValidationRules() {
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
	protected function GetSyslogPublisher() {
		if(empty($this->SyslogPublisher)) {
			$this->SyslogPublisher = new RSyslog(sprintf('%s:%s',
																									 $this->HostName,
																									 $this->Port),
																					 $this->Timeout);
		}

		return $this->SyslogPublisher;
	}

	/**
	 * Defines the related database table name. Table name must be passed as a
	 * parameter.
	 *
	 * @param string HostName 	The address of RSyslog Log Server.
	 * @param int Port The port to use to connect to RSyslog server.
	 */
	public function __construct($HostName, $Port) {
		parent::__construct();

		$this->HostName = $HostName;
		$this->Port = $Port;

		$this->_SetRSyslogValidationRules();
	}

	/**
	 * Builds a Message that will be sent to a RSyslog Server.
	 *
	 * @param array LogFields An associative array of fields describing a Log Entry.
	 * @return string A string representing the message.
	 */
	protected function BuildSysLogMessage(array &$LogFields) {
		return new SyslogMessage($LogFields['Message'],
														 $LogFields['Facility'],
														 $LogFields['Severity'],
														 $LogFields['TimeStamp']);
	}


	/**
	 * Sends a Message to a RSyslog server.
	 *
	 * @param string Message The Message to be sent.
	 * @return bool True if message was sent correctly, False otherwise.
	 */
	protected function PublishMessage($Message) {
		$Result = $this->GetSyslogPublisher()->Send($Message);
		if($Result === true) {
			return true;
		}

		// In case of error, RSysLog publisher returns an array containing an Error Number
		// and an Error Message
		trigger_error(sprintf('Error occurred sending log to Remote Syslog Server. Error number: %d. Error Message: %s',
													$Result[0],
													$Result[1]));
		return false;
	}

	/**
	 * Save a Log Entry to RSyslog Server.
	 *
   * @param array LogFields An associative array of Log Entry fields.
   * @return bool True if the message was sent correctly, False otherwise.
	 */
	public function Save(&$LogFields) {
		// Validate posted data
		//if(!$this->Validate($LogFields)) {
		//	return false;
		//}
		$Message = $this->BuildSysLogMessage($LogFields);

		return $this->PublishMessage($Message);
	}
}
