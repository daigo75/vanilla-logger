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

	/**
	 * Configures the System Appender, which will store all Log entries in
	 * LoggerSysLog table, in Vanilla database. Log messages will always be saved
	 * to this table, whether other Appenders have been configured or not.
	 */
	protected function install_system_appender() {
		$AppenderConfigValues = array(
			'AppenderID' => 1,
			'AppenderName' =>'System',
			'AppenderClass' => 'LoggerAppenderVanillaDB',
			'AppenderDescription' => 'System Logger - It\'s always enabled and saves to a table in Vanilla\'s Database.',
			'IsSystem' => 1,
			'IsEnabled' => 1,
			'Configuration' => '{"params":{"table":"LoggerSysLog","createtable":"1"}}',
		);

		Gdn::SQL()->Replace('LoggerAppenders',
												$AppenderConfigValues,
												array('AppenderID' => 1),
												true);
	}

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
		$this->create_logger_appenders_view();
		$this->install_system_appender();
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
