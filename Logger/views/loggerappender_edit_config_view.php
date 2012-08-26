<?php	if (!defined('APPLICATION')) exit();
/*
Copyright 2012 Diego Zanella IT Services
This file is part of Logger Plugin for Vanilla Forums.

Logger Plugin is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any later version.
Logger Plugin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with Logger Plugin .  If not, see <http://www.gnu.org/licenses/>.

Contact Diego Zanella at diego [at] pathtoenlightenment [dot] net
*/

// Store the name of the View to use to handle configuration parameters of a specific Appender
$AppenderConfigView = $this->Data['AppenderConfigView'];

// Indicates if we're configuring a new appender or editing an existing one.
$AppenderID = $this->Form->GetValue('AppenderID');
$IsNewAppender = empty($AppenderID) ? true : false;
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
					echo $this->Form->Label(T('Appender Type'));
					echo Wrap($this->Form->GetValue('AppenderClass'),
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
					// Set IsEnabled to True if we're adding a new Appender
					if($IsNewAppender) {
						$this->Form->SetValue('IsEnabled', 1);
					}
					echo $this->Form->CheckBox('IsEnabled',
																		 T('<strong>Enable</strong>. Tick this box if you want to enable this Appender.'),
																		 array('value' => 1,));
				?>
			</li>
		</ul>
		<?php
			// If Controller passed the name of a specific configuration View for
			// the Appender, load it. Else, display a simple texarea where User
			// can enter raw JSON.
			if($AppenderConfigView) {
				echo $this->FetchView($AppenderConfigView);
			}
			else {
				echo "<ul>\n";
				echo "<li>\n";
				echo $this->Form->Label(T('JSON Configuration'), 'Configuration');
				echo Wrap(T('Enter the parameters for the Appender, in JSON format. You can use specifications for the XML ' .
										'on <a href="http://logging.apache.org/log4php/docs/configuration.html">Log4php website</a>. as ' .
										'a reference.'
										),
									'div',
									array('class' => 'Info'));
				echo Wrap(T('<strong>Important:</strong> in this box, you only have to enter the parameters and the layout ' .
										'for the appender (i.e. the equivalent of the <code>&lt;layout&gt;</code> and <code>&lt;param&gt;</code> ' .
										'nodes of the XML configuration for an Appender. The rest of the configuration will be built automatically.'),
									'div',
									array('class' => 'Info'));
				echo $this->Form->TextBox('Configuration',
																	array('multiline' => true,));
			}
			echo "</li>\n";
			echo "</ul>\n";
		?>
		<div class="Buttons">
			<?php
				echo $this->Form->Hidden('AppenderID');
				echo $this->Form->Hidden('AppenderClass');
				echo $this->Form->Button(T('Save'), array('Name' => 'Save',));
				echo $this->Form->Button(T('Cancel'), array('Name' => 'Cancel',));
			?>
		</div>
	</fieldset>
	<?php echo $this->Form->Close(); ?>
</div>
