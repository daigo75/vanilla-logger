<?php	if (!defined('APPLICATION')) exit();
/**
 * Copyright 2012 Diego Zanella
 * This file is part of Logger Plugin for Vanilla Forums.
 *
 * Plugin is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or (at your
 * option) any later version.
 * Plugin is distributed in the hope that it will be
 * useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General
 * Public License for more details.
 * You should have received a copy of the GNU General Public License along with
 * Logger Plugin. If not, see http://opensource.org/licenses/GPL-2.0.
 *
 * @package Logger Plugin
 * @author Diego Zanella <diego@pathtoenlightenment.net>
 * @copyright Copyright (c) 2011 Diego Zanella (http://dev.pathtoenlightenment.net)
 * @license http://opensource.org/licenses/GPL-2.0 GPL 2.0
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
