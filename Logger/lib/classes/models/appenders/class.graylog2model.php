<?php if (!defined('APPLICATION')) exit();

// Load GELF Libraries
require(LOGGER_PLUGIN_EXTERNAL_PATH . '/Graylog2-gelf-php/GELFMessage.php');
require(LOGGER_PLUGIN_EXTERNAL_PATH . '/Graylog2-gelf-php/GELFMessagePublisher.php');

/**
 * Graylog2 Log Model
 *
 * @package LoggerPlugin
 */
class Graylog2Model extends Gdn_Model {
	/// @var GELFMessagePublisher
	protected $GELFMessagePublisher;

	/**
	 * @var array An associative array mapping Log4php Log levels to the
	 * equivalents used by Graylog2. Note that Graylog2 uses Syslog level coding,
	 * which has more levels than the ones provided by Log4php. For this reason,
	 * not all Log levels match, and some had to be ignored.
	 */
	protected $LogLevelMap = array(
		LoggerLevel::FATAL => GELFMessage::EMERGENCY,
		// Log4php has less Log levels. To err on the safe side, Fatal has been
		// translated to Emergency, while Alert and Critical have been ignored.
    // => GELFMessage::ALERT,
    // => GELFMessage::CRITICAL = 2,
		LoggerLevel::ERROR => GELFMessage::ERROR,
		LoggerLevel::WARN => GELFMessage::WARNING,
		LoggerLevel::INFO => GELFMessage::NOTICE,
		LoggerLevel::DEBUG => GELFMessage::INFO,
		LoggerLevel::TRACE => GELFMessage::DEBUG,
	);

	const GRAYLOG2_DEFAULT_PORT = 12201;
	const GRAYLOG2_DEFAULT_CHUNK_SIZE = 1420;


	/**
	 * Set Validation Rules that apply when saving a new row in Cron Jobs History.
	 *
	 * @return void
	 */
	protected function _SetGraylog2ValidationRules() {
		//$this->Validation = &new Gdn_Validation();

		// Set additional Validation Rules here. Please note that formal validation
		// is done automatically by base Model Class, by retrieving Schema
		// Information.
	}

	protected function GetPublisher() {
		if(empty($this->GELFMessagePublisher)) {
			// Instantiate the Message Publisher that will be used to communicate with
			// Graylog2 Server
			$this->GELFMessagePublisher = new GELFMessagePublisher($this->HostName,
																														 $this->Port,
																														 $this->ChunkSize);
		}

		return $this->GELFMessagePublisher;
	}

	/**
	 * Defines the related database table name. Table name must be passed as a
	 * parameter.
	 *
	 * @param TableName The name of the table that the Model will manipulate.
	 * @throws an Exception if no Table Name has been provided.
	 */
	public function __construct($HostName, $Port, $ChunkSize) {
		parent::__construct();

		$this->HostName = $HostName;
		$this->Port = $Port;
		$this->ChunkSize = $ChunkSize;

		$this->_SetGraylog2ValidationRules();
	}

	/**
	 * Translates a Log4php Log Level into the correspondant Graylog2 level.
	 *
	 * @param LogLevel The Log4php Log Level to be translated.
	 * @return A Graylog2 Log Level.
	 */
	protected function GetGraylLog2Level($LogLevel) {
		return $this->LogLevelMap[$LogLevel];
	}

	/**
	 * Builds a GELF Message that will be sent to a Graylog2 Server.
	 *
	 * @param LogFields An associative array of fields describing a Log Entry.
	 * @return A GELF Message instance.
	 */
	protected function BuildGELFMessage(array &$LogFields) {
		$Message = new GELFMessage();

		$Message->setAdditional('LoggerName', $LogFields['LoggerName']);
		$Message->setLevel($this->GetGraylLog2Level($LogFields['Level']));
		$Message->setShortMessage($LogFields['Message']);
		$Message->setAdditional('Thread', $LogFields['Thread']);
		$Message->setAdditional('ClassName', $LogFields['ClassName']);
		$Message->setAdditional('MethodName', $LogFields['MethodName']);
		$Message->setFile($LogFields['FileName']);
		$Message->setLine($LogFields['LineNumber']);
		$Message->setTimestamp($LogFields['TimeStamp']);

		$Message->setFullMessage($LogFields['Exception']);

		// This value is not produced, nor managed by Log4php, but Graylog2 can
		// accept it, therefore it's passed to the server as an additional detail.
		$Message->setHost(gethostname());

		return $Message;
	}

	/**
	 * Sends a GELF Message to a Graylog2 Server.
	 *
	 * @param Message The GELF Message to be sent.
	 * @return True if message was sent correctly, False otherwise.
	 */
	protected function PublishMessage(GELFMessage $Message) {
		return $this->GetPublisher()->publish($Message);
	}

	/**
	 * Save a Log Entry to GrayLog2 Server.
	 *
   * @param array LogFields An associative array of Log Entry fields.
   * @return True if the message was sent correctly, False otherwise.
	 */
	public function Save(&$LogFields) {
		// Validate posted data
		//if(!$this->Validate($LogFields)) {
		//	return false;
		//}

		$Message = $this->BuildGELFMessage($LogFields);

		try {
			return $this->PublishMessage($Message);
		}
		catch(Exception $e) {
			// TODO Find a graceful way to handle and display any exception. Logging it could be complicated, since the log will trigger this method again, leading to an infinite recursion.
			return false;
		}
	}
}
