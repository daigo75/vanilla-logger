<?php if (!defined('APPLICATION')) exit();
/*
Copyright 2012 Diego Zanella IT Services
This file is part of Logger Plugin for Vanilla Forums.

Logger Plugin is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any later version.
Logger Plugin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with Logger Plugin .  If not, see <http://www.gnu.org/licenses/>.

Contact Diego Zanella at diego [at] pathtoenlightenment [dot] net
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
			->Column('AppenderName', 'varchar(100)')
			->Column('AppenderType', 'varchar(40)')
			->Column('AppenderDescription', 'varchar(255)')
			->Column('IsSystem', 'uint', 0, 'index')
			->Column('IsEnabled', 'uint', 1, 'index')
			->Column('Configuration', 'text', TRUE)
			->Column('DateInserted', 'datetime', FALSE)
			->Column('InsertUserID', 'int', TRUE)
			->Column('DateUpdated', 'datetime', FALSE)
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
					"    ,A.AppenderType\n" .
					"    ,A.IsSystem\n" .
					"    ,A.IsEnabled\n" .
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
