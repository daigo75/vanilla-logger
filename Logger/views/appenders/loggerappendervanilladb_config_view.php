<?php	if (!defined('APPLICATION')) exit();
/**
{licence}
*/


?>
<div class="LoggerAppenderVanillaDB">
	<?php
		echo Wrap('Vanilla Database Parameters', 'h3');
	?>
	<ul class="Params">
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
</div>
