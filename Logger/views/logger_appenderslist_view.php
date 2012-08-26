<?php if (!defined('APPLICATION')) exit();
/*
Copyright 2012 Diego Zanella IT Services
This file is part of Logger Plugin for Vanilla Forums.

Logger Plugin is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any later version.
Logger Plugin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with Logger Plugin .  If not, see <http://www.gnu.org/licenses/>.

Contact Diego Zanella at diego [at] pathtoenlightenment [dot] net
*/

// Indicates how many columns there are in the table that shows the list of
// configured Log Appenders. It's mainly used to set the "colspan" attributes of
// single-valued table rows, such as Title, or the "No Results Found" message.
define('APPENDERS_TABLE_COLUMNS', 5);

// The following HTML will be displayed when the DataSet is empty.
$OutputForEmptyDataSet = Wrap(T('No Appenders configured.'),
															'td',
															array('colspan' => APPENDERS_TABLE_COLUMNS,
																		'class' => 'NoResultsFound',)
															);

$AppendersDataSet = $this->Data['AppendersDataSet'];
?>
<div class="Logger">
	<div class="Header">
		<?php include('logger_admin_header.php'); ?>
	</div>
	<div class="Content">
		<?php
			echo $this->Form->Open();
			echo $this->Form->Errors();
		?>
		<h3><?php echo T('Configured Appenders'); ?></h3>
		<div class="Info">
			<?php
				echo Wrap(T('Here you can configure the Appenders that will be hooked to the main Logger.'), 'p');
			?>
		</div>
		<div class="FilterMenu">
		<?php
			echo Anchor(T('Add Appender'), LOGGER_APPENDER_ADD_URL, 'SmallButton');
		?>
		</div>
		<table id="AppendersList" class="display AltRows">
			<thead>
				<tr>
					<th><?php echo T('Name'); ?></th>
					<th><?php echo T('Type'); ?></th>
					<th><?php echo T('Description'); ?></th>
					<th><?php echo T('Enabled?'); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tfoot>
			</tfoot>
			<tbody>
				<?php
					$AppendersDataSet = $this->Data['AppendersDataSet'];

					// If DataSet is empty, just print a message.
					if(empty($AppendersDataSet)) {
						echo $OutputForEmptyDataSet;
					}

					// TODO Implement Pager.
					// Output the details of each row in the DataSet
					foreach($AppendersDataSet as $Appender) {
						//var_dump($Appender);
						if($Appender->IsSystem) {
							echo "<tr class=\"SystemAppender\">\n";
						}
						else {
							echo "<tr>\n";
						}

						// Output Appender Name
						echo Wrap(Gdn_Format::Text($Appender->AppenderName), 'td', array('class' => 'AppenderName',));
						// Output Appender Type
						echo Wrap(Gdn_Format::Text($Appender->AppenderClass), 'td', array('class' => 'AppenderClass',));
						// Output Appender Description
						echo Wrap(Gdn_Format::Text($Appender->AppenderDescription), 'td', array('class' => 'AppenderDescription',));
						// Output "Enabled" indicator
						$EnabledText = ($Appender->IsEnabled == 1) ? T('Yes') : '';
						echo Wrap(Gdn_Format::Text($EnabledText), 'td', array('class' => 'Enabled',));

						echo "<td>\n";
						// Show Edit/Delete Buttons only if Appender is not a System
						// Appender. System Appenders are not supposed to be reconfigured by
						// the Admins
						// TODO Add a "View" button, which allows an Admin to go to a read-only Edit Page to see Appender's Settings
						if(!$Appender->IsSystem) {
							// Output Add/Edit button
							echo Anchor(T('Edit'),
													sprintf('%s?%s=%s&%s=%s',
																	LOGGER_APPENDER_EDIT_URL,
																	LOGGER_ARG_APPENDERID,
																	Gdn_Format::Url($Appender->AppenderID),
																	LOGGER_ARG_APPENDERTYPE,
																	$Appender->AppenderClass),
													'SmallButton AddEditAppender');
							// Output Delete button
							echo Anchor(T('Delete'),
													sprintf('%s?%s=%s',
																	LOGGER_APPENDER_DELETE_URL,
																	LOGGER_ARG_APPENDERID,
																	Gdn_Format::Url($Appender->AppenderID)),
													'SmallButton DeleteAppender');
						}
						echo "</td>\n";
						echo "</tr>\n";
					}
				?>
			 </tbody>
		</table>
		<?php
			 echo $this->Form->Close();
		?>
	</div>
</div>
