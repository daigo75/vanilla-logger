<?php if (!defined('APPLICATION')) exit();
/*
Copyright 2012 Diego Zanella IT Services
This file is part of Logger Plugin for Vanilla Forums.

Logger Plugin is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any later version.
Logger Plugin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with Logger Plugin .  If not, see <http://www.gnu.org/licenses/>.

Contact Diego Zanella at diego [at] pathtoenlightenment [dot] net
*/
?>
<div class="LoggerPlugin">
	<div class="PluginHeader">
		<?php include('logger_admin_header.php'); ?>
	</div>
	<div class="PluginContent">
		<?php
			echo $this->Form->Open();
			echo $this->Form->Errors();
		?>
		<fieldset>
			<legend>
				<h3><?php echo T('Date Range'); ?></h3>
				<p>
					<?php
					echo T('In this section you can view the Log entries recored by Logger Plugin.');
					?>
				</p>
			</legend>
			<div class="FilterMenu">
				<ul>
					 <li><?php
							echo $this->Form->Label(T('Start Date'), 'DateFrom');
							echo $this->Form->Date('DateFrom');
					 ?></li>
					 <li><?php
							echo $this->Form->Label(T('End Date'), 'DateTo');
							echo $this->Form->Date('DateTo');
					 ?></li>
					 <li><?php
							//echo $this->Form->Label(T('Results per Page'), 'ResultsPerPage');
							//echo $this->Form->Textbox('ResultsPerPage');
							echo $this->Form->Hidden('ResultsPerPage', array('value' => CRON_DEFAULT_HISTORYJOBSPERPAGE,));
					 ?></li>
					 <li><?php
						// TODO Add multi-select box where users can choose which log levels to display
					 ?></li>
				</ul>
			</div>
		</fieldset>
		<?php
			echo $this->Form->Close('Refresh');
		?>
		<div class="Results">
			<?php
				$LogDataSet = $this->Data['LogDataSet'];

				include('logger_viewlog_details.php');
			?>
		</div>
	</div>
</div>
