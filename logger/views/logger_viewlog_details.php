<?php if (!defined('APPLICATION')) exit();
/**
 * Copyright 2012 Diego Zanella
 * This file is part of CronJobs Plugin for Vanilla Forums.
 *
 * CronJobs Plugin is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or (at your
 * option) any later version.
 * Plugin is distributed in the hope that it will be
 * useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General
 * Public License for more details.
 * You should have received a copy of the GNU General Public License along with
 * CronJobs Plugin. If not, see http://opensource.org/licenses/GPL-2.0.
 *
 * @package CronJobs Plugin
 * @author Diego Zanella <diego@pathtoenlightenment.net>
 * @copyright Copyright (c) 2011 Diego Zanella (http://dev.pathtoenlightenment.net)
 * @license http://opensource.org/licenses/GPL-2.0 GPL 2.0
*/

/**
 * This template is a snippet that has to be loaded by parent views, such as
 * cronjobhistory_view.php. It expects variable $LogDataSet
 * to be already populated, and, if it is, it outputs a table containing the
 * Cron Jobs History within a date range.
 *
 * This snippet has been extracted to a separate file to allow reusing it in
 * different places (e.g. in Views which allow Users to choose different
 * parameters, or in a Widget on the front end).
 */

// No point in proceeding if there's not even a DataSet to begin with.
if(!isset($LogDataSet)) { return; }

// Indicates how many columns there are in the table that shows data from the
// Cron Jobs History. It's mainly used to set the "colspan" attributes of
// single-valued table rows, such as Title, or the "No Results Found" message.
define('LOG_TABLE_COLUMNS', 9);

// The following HTML will be displayed when the DataSet is empty.
$OutputForEmptyDataSet = Wrap(T('No results found. Please try using different parameters.'),
															'td',
															array('colspan' => LOG_TABLE_COLUMNS,
																		'class' => 'NoResultsFound',)
															);

// TODO Alter CSS to make log view page better structured
?>
	<table id="LogDetails" class="display">
		<thead>
			<tr>
				<th class="TimeStamp"><?php echo T('Timestamp'); ?></th>
				<th class="LoggerName"><?php echo T('Logger Name'); ?></th>
				<th class="Level"><?php echo T('Level'); ?></th>
				<th class="Message"><?php echo T('Message'); ?></th>
				<th class="Thread"><?php echo T('Thread'); ?></th>
				<th class="ClassName"><?php echo T('Class Name'); ?></th>
				<th class="MethodName"><?php echo T('Method'); ?></th>
				<th class="FileName"><?php echo T('File Name'); ?></th>
				<th class="LineNumber"><?php echo T('Line Number'); ?></th>
			</tr>
		</thead>
		<tfoot>
		</tfoot>
		<tbody>
			<?php
				// If DataSet is empty, just print a message.
				if(empty($LogDataSet)) {
					echo $OutputForEmptyDataSet;
				}

				// TODO Implement Pager.
				// Output the details of each row in the DataSet
				foreach($LogDataSet as $LogEntry) {
					echo "<tr>\n";
					// Log Timestamp
					echo Wrap($LogEntry->TimeStamp, 'td', array('class' => 'Timestamp',));
					// Output Logger Name
					echo Wrap(Gdn_Format::Text($LogEntry->LoggerName), 'td', array('class' => 'LoggerName',));
					// Output Log Level
					echo Wrap($LogEntry->Level, 'td', array('class' => 'Level',));
					// Output Log Message
					echo Wrap(Gdn_Format::Text($LogEntry->Message), 'td', array('class' => 'Message',));
					// Output Thread Number
					echo Wrap($LogEntry->Thread, 'td', array('class' => 'Thread',));
					// Output Class Name
					echo Wrap(Gdn_Format::Text($LogEntry->ClassName), 'td', array('class' => 'ClassName',));
					// Output Method Name
					echo Wrap(Gdn_Format::Text($LogEntry->MethodName), 'td', array('class' => 'MethodName',));
					// Output File Name
					echo Wrap(Gdn_Format::Text($LogEntry->FileName), 'td', array('class' => 'FileName',));
					// Output Line Number
					echo Wrap($LogEntry->LineNumber, 'td', array('class' => 'Result',));
					echo "</tr>\n";
				}
			?>
		 </tbody>
	</table>
