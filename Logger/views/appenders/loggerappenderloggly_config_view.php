<?php	if (!defined('APPLICATION')) exit();
/*
{licence}
*/

?>
<div class="LoggerAppenderLoggly">
	<?php
		echo Wrap('Loggly Server Parameters', 'h3');
	?>
	<ul class="Params">
		<?php
			// Load the View Snippet containing the interface for the configuration of the
			// layout to be used by the Appender.
			//include 'loggerappender_layout_config_snippet.php';
		?>
		<li>
			<?php
				echo $this->Form->Label(T('Input Key'), 'InputKey');
				echo Wrap(T('Enter the SHA Key to be used to send the Log Messages to Loggly. <br />' .
										'To find this information, open your <a href="http://www.loggly.com">' .
										'Loggly</a> Dashboard, navigate to the <strong>Inputs</strong> tab and ' .
										'click on the Input that you would like to receive the log. At the bottom ' .
										'of the page you will see a <strong>Configuration</strong> ' .
										'section, which displays the Input Key for that Input. Simply copy ' .
										'that key in this field.'),
									'div',
									array('class' => 'Info'));
				echo $this->Form->TextBox('InputKey',
																	array('maxlength' => '100',));
			?>
		</li>
	</ul>
</div>
