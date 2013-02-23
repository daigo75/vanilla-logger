<?php if (!defined('APPLICATION')) exit();
/**
{licence}
*/
 
?>
<div class="LoggerPlugin">
	<div class="Content">
		<fieldset>
			<legend>
				<h3><?php echo T('Logger for Vanilla - Basic version'); ?></h3>
				<p>
					<?php
					echo sprintf(T('Basic version is provided without a GUI. If you wish to modify the ' .
												 'configuration, you can edit file <i>config.xml</i>, located in <i>%s</i>.'),
											 PATH_PLUGINS . '/Logger/');
					?>
				</p>
			</legend>
		</fieldset>
		<?php
			 echo $this->Form->Close();
		?>
	</div>
</div>
