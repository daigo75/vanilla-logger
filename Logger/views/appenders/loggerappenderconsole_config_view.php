<?php	if (!defined('APPLICATION')) exit();
/**
{licence}
*/

$ConsoleTargets = array('stdout' => T('Standard Out (stdout)'),
												'stderr' => T('Standard Error (stderr)'),);

?>
<ul class="LoggerAppenderConsole Params">
	<?php
		// Load the View Snippet containing the interface for the configuration of the
		// layout to be used by the Appender.
		include 'loggerappender_layout_config_snippet.php';
	?>
	<li>
		<?php
			echo $this->Form->Label(T('Target'), 'Target');
			echo Wrap(T('Enter a description for the Appender. This is useful if you have several Appenders
									of the same type and you need to distinguish between them.'),
								'div',
								array('class' => 'Info'));
			echo $this->Form->DropDown('Target',
																 $ConsoleTargets,
																 array('name' => 'Target',));
		?>
	</li>
</ul>
