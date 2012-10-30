<?php if (!defined('APPLICATION')) exit();
/**
{licence}
*/

// This associative array will be used to give Users a way to easily choose a level
$LoggerLevels = array(
	'OFF' => T('Disabled'),
	'FATAL' => T('Fatal'),
	'ERROR' => T('Error'),
	'WARN' => T('Warning'),
	'INFO' => T('Information'),
	'DEBUG' => T('Debug'),
	'TRACE' => T('Trace'),
	'ALL' => T('All'),
);

$CurrentLoggerLevel = C('Plugin.Logger.LogLevel');

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
		<fieldset>
			<legend>
				<h3><?php echo T('General Settings'); ?></h3>
				<p>
					<?php
					echo T('In this section you can find some settings that will apply to the default (System) logger.');
					?>
				</p>
			</legend>
			<ul>
				<li><?php
					echo $this->Form->Label(T('Logger Level'), 'Plugin.Logger.LogLevel');
					echo Wrap(T('Select the Log Level. Messages with a level lower than the one selected ' .
											'will be ignored. <strong>Example</strong>: if you select "<i>Warning</i>", ' .
											'messages logged as <i>Trace</i>, <i>Debug</i> and <i>Info</i> will be ignored.'),
										'div',
										array('class' => 'Info',));
					echo $this->Form->DropDown('Plugin.Logger.LogLevel',
																		 $LoggerLevels,
																		 array('id' => 'LoggerLevel',
																					 'value' => $CurrentLoggerLevel,));
				?></li>
			</ul>
		</fieldset>
		<?php
			 echo $this->Form->Close('Save');
		?>
	</div>
</div>
