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

	/*** XML Convenience Functions ***/
	/**
	 * Adds a child node to a given SimpleXMLElement.
	 *
	 * @param Node The node to which the <layout> node should be added.
	 * @param ChildNodeName The name to assign to the child node.
	 * @param Attributes An associative array of attributes to assign to the child node.
	 * @param Value The value to assign to the child node.
	 * @return void.
	 */
	protected function AddParameterNode(SimpleXMLElement $Node, $ChildNodeName = 'param', array $Attributes, $Value = null) {
		$ParamNode = $Node->addChild($ChildNodeName, $Value);
		foreach($Attributes as $ParamName => $ParamValue) {
			$ParamNode->addAttribute($ParamName, $ParamValue);
		}
		return;
	}

	/**
	 * Adds a <layout> node to a given SimpleXMLElement.
	 *
	 * @param Node The node to which the <layout> node should be added.
	 * @param Layout The name of the layout.
	 * @return void.
	 */
	protected function AddLayoutNode(SimpleXMLElement $Node, $Layout) {
		// There's no need to add a layout node if Layout name is empty.
		if(empty($Layout)) {
			return;
		}

		$this->AddParameterNode($Node, 'layout', array('class' => $Layout,));
		return;
	}

	/**
	 *  Adds a child parameter node to a SimpleXMLElement, setting its "name" and
	 *  "value" attributes by taking them from a field extracted from a list of
	 *  fields. The "name" attribute will be set to the field name, while the
	 *  "value" will be set to the Field value.
	 *
	 * @param Node The node to which the <param> node should be added.
	 * @param FormValues An associative array of fields (usually the one received
	 * when a Form is posted) from which the Field will be extracted.
	 * @param FieldName The name of the field to extract.
	 * @return void.
	 */
	protected function AddParamNodeFromField(SimpleXMLElement $Node, array $FormValues, $FieldName) {
		$this->AddParameterNode($Node, 'param', array('name' => $FieldName,
																									'value' => $FormValues[$FieldName]));
	}


	/**
	 * Build SQL query to retrieve data from the LoggerAppenders Table.
	 */
	protected function PrepareLoggerAppendersQuery() {
		$Query = $this->SQL
			->Select('VLA.AppenderID')
			->Select('VLA.AppenderName')
			->Select('VLA.AppenderType')
			->Select('VLA.AppenderDescription')
			->Select('VLA.IsSystem')
			->Select('VLA.IsEnabled')
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
			throw new Logger_InvalidValidationException(T('Invalid Validation parameter. Parameter can only be NULL, or be an instance of LoggerAppenderConfigValidation'));
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
		// Appender ID must be a number, therefore there's no point in running a
		// query if it's empty or non-numeric.
		if(empty($AppenderID) || !is_numeric($AppenderID)) {
			return null;
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
		$Insert = ($this->GetAppenderSettings($AppenderID) == null);

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
