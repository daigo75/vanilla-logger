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
