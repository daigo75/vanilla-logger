<?php	if (!defined('APPLICATION')) exit();

/**
 * This Appender is used to write to a table into Vanilla's Database by using
 * the Objects provided by Vanilla's framework. Such objects will be
 * retrieved automatically by the Appender when it's instantiated. For this
 * reason, this Appender will only work when used within the forum, it can't
 * be exported as standalone.
 */

// TODO Implement Appender to Database using Vanilla Database Objects.
class LoggerAppenderVanillaDB extends LoggerAppender {
	// Log Table Model
	protected $LogModel;

	protected $Table;
	protected $CreateTable;

	public function getCreateTable() {
		return $this->CreateTable;
	}

	public function setCreateTable($Value) {
		$this->CreateTable = $Value;
	}

	public function getTable() {
		return $this->Table;
	}

	public function setTable($Value) {
		$this->Table = $Value;
	}

	public function __construct() {
		parent::__construct();

		// Retrieve Vanilla's Database Objects
		$this->Database = &Gdn::Database();
	}

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

			// Instantiate the Model that will write to the Log Table
			$this->LogModel = &new VanillaDBLogModel($this->Table);
		}
		catch (Exception $e) {
			throw new LoggerException($e);
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
		//var_dump($event->getLocationInformation());
		$this->LogModel->Save($this->PrepareLogFields($event));
	}
}
