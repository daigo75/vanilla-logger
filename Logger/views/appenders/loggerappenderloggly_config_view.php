<?php	if (!defined('APPLICATION')) exit();

?>
<div class="LoggerAppenderGraylog2">
	<?php
		echo Wrap('Graylog 2 Server Parameters', 'h3');
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
										'To find this information, open your Loggly Dashboard, navigate to ' .
										'the Inputs tab and click on the Input that you would like to receive ' .
										'the log. At the bottom of the page you will see a Configuration ' .
										'section, which displays the Input Key for that Input. Simply copy ' .
										'that key in this field.'),
									'div',
									array('class' => 'Info'));
				echo $this->Form->TextBox('InputKey',
																	array('maxlength' => '100',));
			?>
		</li>
		<li>
			<?php
				echo $this->Form->Label(T('Port'), 'Port');
				echo Wrap(T('Enter the Port of Graylog2 Server.'),
									'div',
									array('class' => 'Info'));
				echo $this->Form->TextBox('Port',
																	array('maxlength' => '5',));
			?>
		</li>
		<li>
			<?php
				echo $this->Form->Label(T('Chunk Size'), 'ChunkSize');
				echo Wrap(T('Enter the Chunk Size to use to communicate with Graylog2 Server. '.
										'If not sure, leave the default value.'),
									'div',
									array('class' => 'Info'));
				echo $this->Form->TextBox('ChunkSize',
																	array('maxlength' => '8',));
			?>
		</li>
	</ul>
</div>
