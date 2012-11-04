<?php	if (!defined('APPLICATION')) exit();
/**
{licence}
*/

?>
<li>
	<?php
		echo $this->Form->Label(T('Layout'), 'Layout');
		echo Wrap(T('Currently, only <a href="http://logging.apache.org/log4php/docs/layouts/pattern.html">LoggerLayoutPattern</a>, in it\'s default configuration, is supported.'),
							'div',
							array('class' => 'Info',));

		echo $this->Form->Hidden('Layout', array('value' => 'LoggerLayoutPattern',));
		//echo Wrap(T('Please select the layout that will be used by the Appender.'),
		//					'div',
		//					array('class' => 'Info',));
		//echo $this->Form->DropDown('AppenderClass',
		//													 $this->Data['Layouts'],
		//													 array('id' => 'Layout',));
	?>
</li>
