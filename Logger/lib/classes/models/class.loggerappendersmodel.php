<?php if (!defined('APPLICATION')) exit();

/**
 * Logger Appenders Model
 *
 * @package LoggerPlugin
 */

/**
 * This model is used to store Log entries to a table.
  */
class LoggerAppendersModel extends Gdn_Model {
	/**
	 * Set Validation Rules that apply when saving a new row in Cron Jobs History.
	 *
	 * @return void
	 */
	protected function _SetLoggerAppendersValidationRules() {
		$this->Validation = &new Gdn_Validation();

		// Set additional Validation Rules here. Please note that formal validation
		// is done automatically by base Model Class, by retrieving Schema
		// Information.
		$this->Validation->AddRule('ValidAppenderType', 'function:ValidateAppenderType');

		// Validation rules for Appender Type
		$this->Validation->ApplyRule('AppenderType', 'ValidAppenderType', T('Appender Type is not valid. Please select an Appender Type from the ones in the list.'));
	}

	/**
	 * Build SQL query to retrieve data from the LoggerAppenders Table.
	 */
	protected function PrepareLoggerAppendersQuery() {
		$Query = $this->SQL
			->Column('VLA.AppenderID')
			->Column('VLA.AppenderName')
			->Column('VLA.AppenderType')
			->Column('VLA.IsSystem')
			->Column('VLA.IsEnabled')
			->From('v_logger_appenders VLA');
		return $Query;
	}

	/**
	 * Defines the related database table name.
	 */
	public function __construct() {
		parent::__construct('LoggerAppenders');

		$this->_SetLoggerAppendersValidationRules();
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

		// Return the Jobs Started within the Date Range.
		$this->PrepareLoggerAppendersQuery();

		// Add WHERE clauses, if provided
		if(!empty($Wheres)) {
			$this->SQL->Where($Wheres);
		}

		$Result = $this->SQL
			->OrderBy('DateInserted', 'desc')
			->Get();

		return $Result;
	}

	/** Convenience function. It returns a list of all the active Appenders.
	 *
	 * @return A dataset containing the details of all the active Appenders.
	 */
	public function GetActiveAppenders() {
		return $this->Get(array('IsEnabled' => 'True',));
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
	protected function GetAppenderSettings($AppenderID) {
		// Return empty array if no Appender ID is passed
		if(!is_numeric($AppenderID)) {
			return false;
		}

		// Retrieve and return the Appender Settings
		return $this->Get(array('AppenderID' => $AppenderID,))->FirstRow(DATASET_TYPE_ARRAY);
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
		$Insert = ($this->GetAppenderSettings($AppenderID) === false);

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
