<?php	if (!defined('APPLICATION')) exit();
/**
{licence}
*/
?>
<ul class="LoggerAppendereCHO Params">
	<?php
		// Load the View Snippet containing the interface for the configuration of the
		// layout to be used by the Appender.
		include 'loggerappender_layout_config_snippet.php';
	?>
	<li>
		<?php
			echo Wrap(T('If set to true, a <br /> element will be inserted before each ' .
									'line break in the logged message.'),
								'div',
								array('class' => 'Info'));
			echo $this->Form->Checkbox('HtmlLineBreaks',
																 T('HTML Line Breaks'),
																 array('value' => 1));
		?>
	</li>
</ul>
