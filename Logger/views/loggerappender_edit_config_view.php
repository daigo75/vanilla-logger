<?php	if (!defined('APPLICATION')) exit();
/*
{licence}
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
					$AppenderInfo = $this->Data['AppenderInfo'];
					echo Wrap(sprintf(T('%s Appender Configuration'),
														$AppenderInfo['Label']),
										'h2');
					echo Wrap($AppenderInfo['Description'],
										'div',
										array('class' => 'Info',)
										);
				?>
			</li>
		</ul>
		<div class="General">
			<?php
				echo Wrap(T('General Settings'),
										'h3');
			?>
			<ul>
				<li>
					<?php
						echo $this->Form->Label(T('Appender Name'), 'AppenderName');
						echo Wrap(T('Enter a name for the Appender. This is just to help you identifying each Appender.'),
											'div',
											array('class' => 'Info'));
						echo $this->Form->TextBox('AppenderName',
																			array('size' => '50',
																						'maxlength' => '100'));
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
																			array('size' => '100',
																						'maxlength' => '255',));
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
		</div>
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
