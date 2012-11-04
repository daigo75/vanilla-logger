<?php if (!defined('APPLICATION')) exit();
/**
 * {licence}
 */

require('plugin.schema.php');

class LoggerSchema extends PluginSchema {
	/**
	 * Create the table which will store the list of configured Appenders for the
	 * Logger.
	 */
	protected function create_logger_appenders_table() {
		Gdn::Structure()
			->Table('LoggerAppenders')
			->PrimaryKey('AppenderID')
			->Column('AppenderName', 'varchar(100)', FALSE, 'unique')
			->Column('AppenderClass', 'varchar(40)')
			->Column('AppenderDescription', 'varchar(255)')
			->Column('IsSystem', 'uint', 0, 'index')
			->Column('IsEnabled', 'uint', 1, 'index')
			->Column('Configuration', 'text', TRUE)
			->Column('DateInserted', 'datetime', FALSE)
			->Column('InsertUserID', 'int', TRUE)
			->Column('DateUpdated', 'datetime', TRUE)
			->Column('UpdateUserID', 'int', TRUE)
			->Set(FALSE, FALSE);
	}

	///**
	// * Create the System Log table, which will store all Log entries. Log messages
	// * will always be saved to this table, whether other Appenders have been
	// * configured or not.
	// */
	//protected function create_syslog_table() {
	//	Gdn::Structure()
	//		->Table('LoggerSysLog')
	//		->PrimaryKey('LogEntryID')
	//		->Column('LoggerName', 'varchar(100)')
	//		->Column('Level', 'varchar(40)')
	//		->Column('Message', 'varchar(9999)')
	//		->Column('Thread', 'varchar(32)')
	//		->Column('File', 'varchar(400)')
	//		->Column('Line', 'varchar(6)')
	//		->Column('TimeStamp', 'datetime', FALSE, 'index')
	//		->Column('InsertUserID', 'int', TRUE)
	//		->Set(FALSE, FALSE);
	//}

	/**
	 * Creates a View that returns the list of the configured Appenders.
	 */
	protected function create_logger_appenders_view() {
		$Px = $this->Px;
		$Sql = "SELECT\n" .
					"    A.AppenderID\n" .
					"    ,A.AppenderName\n" .
					"    ,A.AppenderClass\n" .
					"    ,A.AppenderDescription\n" .
					"    ,A.IsSystem\n" .
					"    ,A.IsEnabled\n" .
					"    ,A.Configuration\n" .
					"    ,A.DateInserted\n" .
					"    ,A.DateUpdated\n" .
					"FROM\n" .
					"    {$Px}LoggerAppenders A";
		$this->Construct->View('v_logger_appenders', $Sql);
	}

	/**
	 * Create all the Database Objects in the appropriate order.
	 */
	protected function CreateObjects() {
		$this->create_logger_appenders_table();
		//$this->create_syslog_table();
		$this->create_logger_appenders_view();
	}

	/**
	 * Delete the Database Objects.
	 */
	protected function DropObjects() {
		$this->DropView('v_logger_appenders');
		$this->DropTable('LoggerAppenders');
		//$this->DropTable('LoggerSysLog');
	}
}
