<?php	if (!defined('APPLICATION')) exit();
/*
Copyright 2012 Diego Zanella IT Services
This file is part of Logger Plugin for Vanilla Forums.

Logger Plugin is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any later version.
Logger Plugin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with Logger Plugin .  If not, see <http://www.gnu.org/licenses/>.

Contact Diego Zanella at diego [at] pathtoenlightenment [dot] net
*/

?>
<ul id="LoggerAppenderVanillaDB Params">
	<?php
		// Load the View Snippet containing the interface for the configuration of the
		// layout to be used by the Appender.
		//include 'loggerappender_layout_config_snippet.php';
	?>
	<li>
		<?php
			echo $this->Form->Label(T('Table'), 'Table');
			echo Wrap(T('Enter the name of the table where the log entries will be stored.'),
								'div',
								array('class' => 'Info'));
			echo $this->Form->TextBox('Table',
																array('maxlength' => '100',));
		?>
	</li>
	<li>
		<?php
			// Enable CreateTable by default when configuring a new Appender
			$AppenderID = $this->Form->GetValue('AppenderID');
			if(empty($AppenderID)) {
				$this->Form->SetValue('CreateTable', 1);
			}
			echo $this->Form->CheckBox('CreateTable',
																 T('Automatically create Log table (recommended).<br />' .
																	 '<strong>Important</strong>: if you leave this box unticked, you must create the Log table manually, or ' .
																	 'logging will fail.'),
																 array('value' => 1,));
		?>
	</li>
</ul>
