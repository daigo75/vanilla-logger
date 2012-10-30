<?php if (!defined('APPLICATION')) exit();
/**
{licence}
*/

?>
<div class="Logger ConfirmationDialog">
	<?php
		echo $this->Form->Open();
		echo $this->Form->Errors();
	?>
	<fieldset>
		<div class="Title">
			<h3><?php echo T('Confirmation'); ?></h3>
		</div>
		<div class="Message">
			<p>
				<?php
					echo Wrap(sprintf(T('You\'re about to delete appender <strong>%s</strong> (class <strong>%s</strong>).'),
														$this->Form->GetValue('AppenderName'),
														$this->Form->GetValue('AppenderClass')),
										'div',
										array('class' => 'Info',)
										);
					echo Wrap(T('Would you like to continue? This operation cannot be undone!'),
										'div',
										array('class' => 'Info',)
										);
				?>
			</p>
		</div>
		<div>
			<?php
				echo $this->Form->Hidden('AppenderID');
				echo $this->Form->Button(T('OK'), array('Name' => 'OK',));
				echo $this->Form->Button(T('Cancel'));
			?>
		</div>
	</fieldset>
	<?php
		 echo $this->Form->Close();
	?>
</div>
