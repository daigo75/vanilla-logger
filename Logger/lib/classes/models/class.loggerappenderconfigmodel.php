<?php if (!defined('APPLICATION')) exit();

/**
 * Logger Appenders Model
 *
 * @package LoggerPlugin
 */

/**
 * This model is used to store Log entries to a table.
  */
class LoggerAppenderConfigModel extends Gdn_Model {
	/**
	 * Build SQL query to retrieve data from the LoggerAppenders Table.
	 */
	protected function PrepareLoggerAppendersQuery() {
		$Query = $this->SQL
			->Select('VLA.AppenderID')
			->Select('VLA.AppenderName')
			->Select('VLA.AppenderClass')
			->Select('VLA.AppenderDescription')
			->Select('VLA.IsSystem')
			->Select('VLA.IsEnabled')
			->Select('VLA.Configuration')
			->Select('VLA.DateInserted')
			->From('v_logger_appenders VLA');
		return $Query;
	}

	/**
	 * Defines the related database table name.
	 *
	 * @param Validation An instance of LoggerAppenderConfigValidation. This will
	 * be used for validation upon saving. It can also be NULL, in which case the
	 * module will use the default Validation, which performs basic formal checks
	 * against the Database fields.
	 * @throws Logger_InvalidValidationException if the Validation parameters is
	 * not NULL, but it's not an instance of LoggerAppenderConfigValidation.
	 */
	public function __construct($Validation = null) {
		parent::__construct('LoggerAppenders');

		// Simply skip the Validation parameter if it's empty
		if(empty($Validation)) {
			return;
		}

		// If not empty, Validation must be an instance of LoggerAppenderConfigValidation
		if($Validation instanceof LoggerAppenderConfigValidation) {
			$this->Validation = $Validation;
		}
		else {
			throw new InvalidArgumentException(T('Invalid Validation parameter. Parameter can only be NULL, or be an instance of LoggerAppenderConfigValidation'));
		}
	}

	/**
	 * Returns a DataSet containing a list of the configured API Appenders.
	 *
	 * @param Wheres An associative array of [Field => Where clause] to add to the
	 * query.
	 * @param Limit Limit the amount of rows to be returned. Note: it doesn't
	 * apply to Summary Datasets, as they normally contain one row per total.
	 * @param Offset Specifies from which rows the data should be returned. Used
	 * for pagination. Note: it doesn't apply to Summary Datasets.
	 * @return A DataSet containing a list of the configured API Appenders.
	 */
	public function Get(array $Wheres = null, $Limit = 1000, $Offset = 0) {
		// Set default Limit and Offset, if invalid ones have been passed.
		$Limit = (is_numeric($Limit) && $Limit > 0) ? $Limit : 1000;
		$Offset = (is_numeric($Offset) && $Offset > 0) ? $Offset : 0;

		// Return the configured Appenders
		$this->PrepareLoggerAppendersQuery();

		// Add WHERE clauses, if provided
		if(!empty($Wheres)) {
			$this->SQL->Where($Wheres);
		}

		$Result = $this->SQL
			->OrderBy('IsSystem', 'desc')
			->OrderBy('DateInserted', 'asc')
			->Get();

		return $Result;
	}

	/** Convenience function. It returns a list of all the active Appenders.
	 *
	 * @return A dataset containing the details of all the active Appenders.
	 */
	public function GetActiveAppenders() {
		// Return the configured Appenders
		$this->SQL
			->Select('VLA.AppenderID')
			->Select('VLA.AppenderName')
			->Select('VLA.AppenderClass')
			->From('v_logger_appenders VLA')
			->Where('VLA.IsEnabled', 1)
			->OrderBy('VLA.AppenderID', 'asc');

		$Result = $this->SQL->Get();

		return $Result;
	}

	/**
	 * Returns an array containing a single row with the settings for a specific
	 * Logger Appender.
	 *
	 * @param AppenderID The Id of the Appender for which the data should be
	 * retrieved.
	 * @return An array containing a single row with the settings for the Logger
	 * Appender, or FALSE if no result is found.
	 */
	protected function GetAppenderConfig($AppenderID) {
		// Appender ID must be a number, therefore there's no point in running a
		// query if it's empty or non-numeric.
		if(empty($AppenderID) || !is_numeric($AppenderID)) {
			return null;
		}

		// Retrieve and return the Appender Settings
		return $this->Get(array('AppenderID' => $AppenderID,))->FirstRow(DATASET_TYPE_ARRAY);
	}

	/**
	 * Processes the "Configuration" field from the settings of a Logger Appender
	 * and returns an associative array that can be passed "as is" to Log4php to
	 * configure an Appender.
	 *
	 * @param AppenderID The ID of the Appender for which the settings should be retrieved.
	 * @return An associative array of settings compatible with Log4php format, or
	 * an empty string if configuration was not found, or invalid.
	 */
	public function GetLog4phpSettings($AppenderID) {
		$AppenderConfig = $this->GetAppenderConfig($AppenderID);
		if(empty($AppenderConfig)) {
			return '';
		}
		// Decode the settings saved in Configuration field
		$Settings = json_decode($AppenderConfig['Configuration'], true);
		// Add the LoggerAppender Class, which is stored in a separate field
		$Settings['class'] = $AppenderConfig['AppenderClass'];

		return $Settings;
	}

	/**
	 * Save an Appender's Configuration into the LoggerAppenders.
	 *
   * @param array $FormPostValues An associative array of $Field => $Value pairs
   * that represent data posted from the form in the $_POST or $_GET collection.
   * @return The value of the Primary Key of the row that has been saved, or
   * FALSE if the operation could not be completed successfully.
	 */
	public function Save(&$FormPostValues) {
		// Define the primary key in this model's table.
		$this->DefineSchema();

		// Validate posted data
		if(!$this->Validate($FormPostValues)) {
			return false;
		}

		// Get the Appender ID posted via the form
		$AppenderID = GetValue($this->PrimaryKey, $FormPostValues, false);

		// See if a Appender with the same ID already exists, to decide if ID was posted and decide how to save
		$Insert = ($this->GetAppenderConfig($AppenderID) == null);

		// Prepare all the validated fields to be passed to an INSERT/UPDATE query
		$Fields = &$this->Validation->ValidationFields();
		if($Insert) {
			$this->AddInsertFields($Fields);
			return $this->Insert($Fields);
		}
		else {
			$this->AddUpdateFields($Fields);
			$this->Update($Fields, array($this->PrimaryKey => $AppenderID));
			return $AppenderID;
		}
	}

	/**
	 * Deletes an entry from the Log Table.
	 *
	 * @param AppenderID The ID of the Log Entry to be deleted.
	 * @return LOGGER_OK if Log Entry was deleted successfully, or a numeric error
	 * code if deletion failed.
	 */
	public function Delete($AppenderID) {
		$this->SQL->Delete('LoggerAppenders', array('AppenderID' => $AppenderID,));
	}
}
