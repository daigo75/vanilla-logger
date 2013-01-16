<?php	if (!defined('APPLICATION')) exit();
/**
{licence}
*/

$IsNewAppender = empty($AppenderID) ? true : false;
?>
<ul class="LoggerAppenderConsole Params">
	<?php
		// Load the View Snippet containing the interface for the configuration of the
		// layout to be used by the Appender.
		include 'loggerappender_layout_config_snippet.php';
	?>
	<li>
		<?php
			echo $this->Form->Label(T('File'), 'File');
			echo Wrap(T('Path to the target file. Relative paths are resolved based on ' .
									'the working directory. Please make sure that destination directory ' .
									'is <strong>writable</strong>.'),
								'div',
								array('class' => 'Info'));
			echo $this->Form->TextBox('File', array('name' => 'File',));

			// Set IsEnabled to True if we're adding a new Appender
			if($IsNewAppender) {
				$this->Form->SetValue('AppendToFile', 1);
			}
			echo $this->Form->CheckBox('AppendToFile',
																 T('Append to file'),
																 array('value' => 1,));
			echo Wrap(T('If ticked, log messages will be appended to ' .
									'the file, otherwise the file contents will ' .
									'be overwritten. It is recommended to leave ' .
									'this setting <strong>enabled</strong>, or ' .
									'the log file will be overwritten at every ' .
									'page load.'),
								'div',
								array('class' => 'Info'));
		?>
	</li>
</ul>
