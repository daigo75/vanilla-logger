<?php	if (!defined('APPLICATION')) exit();
/*
Copyright 2012 Diego Zanella IT Services
This file is part of Logger Plugin for Vanilla Forums.

Logger Plugin is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any later version.
Logger Plugin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with Logger Plugin .  If not, see <http://www.gnu.org/licenses/>.

Contact Diego Zanella at diego [at] pathtoenlightenment [dot] net
*/

// TODO Implement View for Console Appender configuration
?>
<div class="Logger AppenderEdit">
	<?php
		echo $this->Form->Open();
		echo $this->Form->Errors();
	?>
	<fieldset>
		<ul>
			<li>
				<?php
					echo Wrap(sprintf(T('Appender Type: %s.'), $this->Form->GetValue('AppenderType')),
										'div',
										array('class' => 'Info',
													));
				?>
			</li>
			<li>
				<?php
					echo $this->Form->Label(T('Appender Name'), 'AppenderName');
					echo Wrap(T('Enter a name for the Appender. This is just to help you identifying each Appender.'),
										'div',
										array('class' => 'Info',
													'maxlength' => '100',
													));
					echo $this->Form->TextBox('AppenderName',
																		array('maxlength' => '100',));
				?>
			</li>
			<li>
				<?php
					echo $this->Form->Label(T('Description'), 'AppenderDescription');
					echo Wrap(T('Enter a description for the Appender. This is useful if you have several Appenders
											of the same type and you need to distinguish between them.'),
										'div',
										array('class' => 'Info'));
					echo $this->Form->TextBox('AppenderDescription',
																		array('maxlength' => '255',));
				?>
			</li>
			<li>
				<?php
					echo $this->Form->CheckBox('IsEnabled',
																		 T('Tick this box if you want to enable this Appender.'),
																		 array('value' => 1,
																					 'checked' => );
				?>
			</li>
		</ul>
		<div class="Buttons">
			<?php
				echo $this->Form->Hidden('AppenderID');
				echo $this->Form->Hidden('AppenderType');
				echo $this->Form->Button(T('Save'), array('Name' => 'Save',));
				echo $this->Form->Button(T('Cancel'));
			?>
		</div>
	</fieldset>
	<?php echo $this->Form->Close(); ?>
</div>
