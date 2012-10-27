<?php if (!defined('APPLICATION')) exit();
/**
{licence}
*/

/**
 * Copyright 2012 Diego Zanella
 * This file is part of Logger Plugin for Vanilla Forums.
 *
 * Plugin is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or (at your
 * option) any later version.
 * Plugin is distributed in the hope that it will be
 * useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General
 * Public License for more details.
 * You should have received a copy of the GNU General Public License along with
 * Logger Plugin. If not, see http://opensource.org/licenses/GPL-2.0.
 *
 * @package Logger Plugin
 * @author Diego Zanella <diego@pathtoenlightenment.net>
 * @copyright Copyright (c) 2011 Diego Zanella (http://dev.pathtoenlightenment.net)
 * @license http://opensource.org/licenses/GPL-2.0 GPL 2.0
*/

/**
 * Validation functions for Logger Appender Configuration.
 *
 */
// TODO Export all these functions to an independent plugin, to allow other plugins to use them.
if(!function_exists('ValidateAppenderClass')){
	/**
	 * Checks if an Appender Class is amongst the available ones.
	 */
	function ValidateAppenderClass($Value, $Field, $FormPostedValues){
		return LoggerPlugin::AppendersManager()->AppenderExists($Value);
	}
}

if (!function_exists('ValidatePositiveInteger')) {
	/**
	 * Check that a value is a positive Integer.
	 */
	function ValidatePositiveInteger($Value, $Field) {
		return ValidateInteger($Value, $Field) &&
					 ($Value > 0);
	}
}

if (!function_exists('ValidateTCPPort')) {
	/**
	 * Check that a value is a valid number for a TCP Port. Valid numbers range
	 * from 1 to 65535.
	 */
	function ValidateTCPPort($Value, $Field) {
		return ValidatePositiveInteger($Value, $Field) &&
					 ($Value <= 65535);
	}
}
