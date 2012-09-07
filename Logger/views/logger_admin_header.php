<?php	if (!defined('APPLICATION')) exit();
/*
Copyright 2012 Diego Zanella IT Services
This file is part of Logger Plugin for Vanilla Forums.

Logger Plugin is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any later version.
Logger Plugin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with Logger Plugin .  If not, see <http://www.gnu.org/licenses/>.

Contact Diego Zanella at diego [at] pathtoenlightenment [dot] net
*/

	// This array will be used to assign specific Classes to each Tab in the page.
	// As of 18/03/2012, it just contains one entry, where they key is the current
	// path and the value is the class "Active".
	// This will be used to highlight the "Active Tab", following the logic
	// "assign class Active to the Tab associated to current path".
	$TabsClasses = array();
	$TabsClasses[$this->Data['CurrentPath']] = 'Active';

	/**
	 * Renders the HTML Markup that will appear on the page as a Tab.
	 *
	 * This function has been introduced to reduce the amount of duplicate HTML
	 * used to render the page.
	 *
	 * @param string Title The label that will be assigned to the Tab.
	 * @param string URL The Title will be transfored into a link, which will
	 * point to this URL.
	 * @param array An associative array of classes to assign to each tab. It's
	 * mainly used to determine which Tab will be appear as "Active".
	 *
	 * @return An HTML string that will be rendered as a Tab via CSS.
	 */
	function RenderTabItem($Label, $URL, array $Classes) {
		$Result = sprintf("<li class=\"%s\">\n" .
											"	<span>%s</span>\n" .
											"</li>\n",
											$Classes[$URL],
											Anchor($Label, $URL));
		return $Result;
	}
?>
	<div>
		<h1><?php echo T($this->Data['Title']); ?></h1>
	</div>
	<div class="Tabs">
		<ul>
			<?php
				echo RenderTabItem(T('Appenders'), LOGGER_APPENDERS_LIST_URL, $TabsClasses);
				echo RenderTabItem(T('General Settings'), LOGGER_GENERALSETTINGS_URL, $TabsClasses);
			?>
		</ul>
	</div>
