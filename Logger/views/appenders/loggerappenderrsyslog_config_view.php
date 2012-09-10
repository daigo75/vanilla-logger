<?php	if (!defined('APPLICATION')) exit();

// Set default value for Remote Syslog Timeout
if($this->Form->GetValue('Timeout') == null) {
	$this->Form->SetValue('Timeout', RSyslogModel::DEFAULT_TIMEOUT);
}

?>
<div class="LoggerAppenderRSyslog">
	<?php
		echo Wrap('RSyslog Server Parameters', 'h3');
	?>
	<ul class="Params">
		<?php
			// Load the View Snippet containing the interface for the configuration of the
			// layout to be used by the Appender.
			//include 'loggerappender_layout_config_snippet.php';
		?>
		<li>
			<?php
				echo $this->Form->Label(T('Host Name'), 'HostName');
				echo Wrap(T('Enter the name or IP Address of the Remote Syslog Server.'),
									'div',
									array('class' => 'Info'));
				echo $this->Form->TextBox('HostName',
																	array('maxlength' => '100',));
			?>
		</li>
		<li>
			<?php
				echo $this->Form->Label(T('Port'), 'Port');
				echo Wrap(T('Enter the Port to communicate with Remote Syslog Server.'),
									'div',
									array('class' => 'Info'));
				echo $this->Form->TextBox('Port',
																	array('maxlength' => '5',));
			?>
		</li>
		<li>
			<?php
				echo $this->Form->Label(T('Timeout'), 'Timeout');
				echo Wrap(T('Enter the Timeout to be used to when sending log message to the server, expressed in <strong>seconds</strong>. '.
										'If not sure, leave the default value.'),
									'div',
									array('class' => 'Info'));
				echo $this->Form->TextBox('Timeout',
																	array('maxlength' => '2',));
			?>
		</li>
	</ul>
</div>
