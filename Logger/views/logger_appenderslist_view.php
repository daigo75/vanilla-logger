<?php if (!defined('APPLICATION')) exit();
/**
{licence}
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
<div class="LoggerPlugin">
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
			echo Anchor(T('Issue test Log message'),
									LOGGER_TESTLOG_URL,
									'SmallButton',
									array('Title' => T('By clicking on this button, a sample Info Log Message will be ' .
																		 'issued to the System Logger and handled according to the ' .
																		 'configuration. This will be useful to ensure that all Appenders ' .
																		 'are working correctly.'),)
									);
			echo Anchor(T('Issue a burst of test Log messages'),
									sprintf('%s/10', LOGGER_TESTLOG_URL),
									'SmallButton',
									array('Title' => T('By clicking on this button, a burst of 50 sample Info Log Messages will be ' .
																		 'issued to the System Logger. At the end, the total amount of time spent for ' .
																		 'logging the messages will be displayed. This will be useful for performance ' .
																		 'testing.'),)
									);
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
						$EnabledText = ($Appender->IsEnabled == 1) ? T('Yes') : T('No');

						// System Appenders cannot be enabled or disabled. For all the others,
						// display a convenient link to enable/disable them with a single click
						if(!$Appender->IsSystem) {
							$EnabledText = Anchor(Gdn_Format::Text($EnabledText),
																		sprintf('%s?%s=%d&%s=%d',
																						LOGGER_APPENDER_ENABLE_URL,
																						LOGGER_ARG_APPENDERID,
																						$Appender->AppenderID,
																						LOGGER_ARG_ENABLEFLAG,
																						($Appender->IsEnabled == 1 ? 0 : 1)),
																		'EnableLink',
																		array('title' => T('Click here to change Appender status (Enabled/Disabled).'),)
																		);
						}

						echo Wrap($EnabledText,
											'td',
											array('class' => 'Enabled',)
											);

						echo "<td>\n";
						// Show Configure/Delete Buttons only if Appender is not a System
						// Appender. System Appenders are not supposed to be reconfigured by
						// the Admins
						// TODO Add a "View" button, which allows an Admin to go to a read-only Edit Page to see Appender's Settings
						if(!$Appender->IsSystem) {
							// Output Configure button
							echo Anchor(T('Configure'),
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
