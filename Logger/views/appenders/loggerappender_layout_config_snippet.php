<?php	if (!defined('APPLICATION')) exit();

?>
<li>
	<?php
		echo $this->Form->Label(T('Layout'), 'Layout');
		echo Wrap(T('Currently, only <a href="http://logging.apache.org/log4php/docs/layouts/ttcc.html">LoggerLayoutTTCC</a> is supported.'),
							'div',
							array('class' => 'Info',));

		echo $this->Form->Hidden('Layout', array('value' => 'LoggerLayoutTTCC',));
		//echo Wrap(T('Please select the layout that will be used by the Appender.'),
		//					'div',
		//					array('class' => 'Info',));
		//echo $this->Form->DropDown('AppenderType',
		//													 $this->Data['Layouts'],
		//													 array('id' => 'Layout',));
	?>
</li>
