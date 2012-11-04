<?php	if (!defined('APPLICATION')) exit();
/**
{licence}
*/

?>
<script type="text/javascript">
	/// A list of Appender Type descriptions
	var AppenderClassesDescriptions = new Array();
	<?php
		foreach($this->Data['AppenderClassesDescriptions'] as $AppenderClass => $AppenderDescription) {
			printf("AppenderClassesDescriptions['%s'] = '%s';\n", $AppenderClass, addslashes($AppenderDescription));
		}
	?>

	/**
	 * Display the Description of an Appender Type.
	 *
	 * @param AppenderClass The Appender Type for which to show the description.
	 * @return void.
	 */
	function ShowAppenderClassDescription(AppenderClass) {
		$('#AppenderDescription').html(AppenderClassesDescriptions[AppenderClass]);
	}

	/**
	 * Initialization.
	 */
	$(document).ready(function(){
		$('#AppenderClass').change(function() {
			// Show the description of currently selected Appender Type
			ShowAppenderClassDescription(this.value);
		})

		// Show description of the Appender type selected by default
		ShowAppenderClassDescription($('#AppenderClass').val());
	});
</script>

<div class="Logger AppenderAdd">
	<?php
		echo $this->Form->Open();
		echo $this->Form->Errors();
	?>
	<fieldset>
		<ul>
			<li>
				<?php
					echo $this->Form->Label(T('Appender Type'), 'AppenderClass');
					echo Wrap(T('Please select the type of Appender you want to add.'),
										'div',
										array('class' => 'Info',));
					ksort($this->Data['AppenderClasses']);
					echo $this->Form->DropDown('AppenderClass',
																		 $this->Data['AppenderClasses'],
																		 array('id' => 'AppenderClass',));
					echo Wrap(T('Info'),
										'div',
										array('id' => 'AppenderDescription',
													'class' => 'Info',));
				?>
			</li>
		</ul>
		<div class="Buttons">
			<?php
				echo $this->Form->Button(T('Cancel'), array('Name' => 'Cancel',));
				echo $this->Form->Button(T('Next'), array('Name' => 'Next',));
			?>
		</div>
	</fieldset>
	<?php echo $this->Form->Close(); ?>
</div>
