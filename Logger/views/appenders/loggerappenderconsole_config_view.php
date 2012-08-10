<?php	if (!defined('APPLICATION')) exit();
/*
Copyright 2012 Diego Zanella IT Services
This file is part of Logger Plugin for Vanilla Forums.

Logger Plugin is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any later version.
Logger Plugin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with Logger Plugin .  If not, see <http://www.gnu.org/licenses/>.

Contact Diego Zanella at diego [at] pathtoenlightenment [dot] net
*/

$ConsoleTargets = array('stdout' => T('Standard Out (stdout)'),
												'stderr' => T('Standard Error (stderr)'),);

?>
<ul id="LoggerAppenderConsole Params">
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
