<?php	if (!defined('APPLICATION')) exit();
/**
{licence}
*/

// Set default value for Remote Syslog Timeout
if($this->Form->GetValue('Timeout') == null) {
	$this->Form->SetValue('Timeout', LoggerAppenderLogglySyslog::DEFAULT_TIMEOUT);
}

// Set default value for Remote Syslog Port
if($this->Form->GetValue('Port') == null) {
	$this->Form->SetValue('Port', LoggerAppenderLogglySyslog::DEFAULT_PORT);
}

?>
<div class="LoggerAppenderLogglySyslog">
	<?php
		echo Wrap('Loggly Remote Syslog Server Parameters', 'h3');
		echo $this->Form->Hidden('HostName', array('value' => 'logs.loggly.com',));
	?>
	<ul class="Params">
		<?php
			// Load the View Snippet containing the interface for the configuration of the
			// layout to be used by the Appender.
			//include 'loggerappender_layout_config_snippet.php';
		?>
		<li>
			<?php
				echo $this->Form->Label(T('Port'), 'Port');
				echo Wrap(T('Enter the Port to communicate with Loggly Remote Syslog Server.'),
									'div',
									array('class' => 'Info'));
				echo $this->Form->TextBox('Port',
																	array('maxlength' => '5',));
			?>
		</li>
		<li>
			<?php
				echo $this->Form->Label(T('Timeout'), 'Timeout');
				echo Wrap(T('Enter the Timeout to be used to when sending log message to Loggly, expressed in <strong>seconds</strong>. '.
										'If not sure, leave the default value.'),
									'div',
									array('class' => 'Info'));
				echo $this->Form->TextBox('Timeout',
																	array('maxlength' => '2',));
			?>
		</li>
	</ul>
</div>
