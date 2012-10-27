<?php	if (!defined('APPLICATION')) exit();
/**
{licence}
*/

// Set default value for Graylog2 Port
if($this->Form->GetValue('Port') == null) {
	$this->Form->SetValue('Port', LoggerAppenderGraylog2::GRAYLOG2_DEFAULT_PORT);
}

// Set default value for Graylog2 Chunk Size
if($this->Form->GetValue('ChunkSize') == null) {
	$this->Form->SetValue('ChunkSize', LoggerAppenderGraylog2::GRAYLOG2_DEFAULT_CHUNK_SIZE);
}

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
				echo $this->Form->Label(T('Host Name'), 'HostName');
				echo Wrap(T('Enter the name or IP Address of Graylog2 Server.'),
									'div',
									array('class' => 'Info'));
				echo $this->Form->TextBox('HostName',
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
				echo Wrap(T('Enter the Chunk Size to use to communicate with Graylog2 Server. ' .
										'It should be set to 1420 for WAN, and to 8154 for LAN. ' .
										'If not sure, leave the default value.'),
									'div',
									array('class' => 'Info'));
				echo $this->Form->TextBox('ChunkSize',
																	array('maxlength' => '8',));
			?>
		</li>
	</ul>
</div>
