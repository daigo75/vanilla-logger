<?php if (!defined('APPLICATION')) exit();
/*
Copyright 2012 Diego Zanella IT Services
This file is part of Logger Plugin for Vanilla Forums.

Logger Plugin is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any later version.
Logger Plugin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with Logger Plugin .  If not, see <http://www.gnu.org/licenses/>.

Contact Diego Zanella at diego [at] pathtoenlightenment [dot] net
*/

// File logger.defines.php must be included by manually specifying the whole
// path. It will then define some shortcuts for commonly used paths, such as
// LOGGER_PLUGIN_LIB_PATH, used just below.
require(PATH_PLUGINS . '/Logger/lib/logger.defines.php');
// LOGGER_PLUGIN_LIB_PATH is defined in logger.defines.php.
//require(LOGGER_PLUGIN_LIB_PATH . '/logger.validation.php');

require(PATH_PLUGINS . '/Logger/lib/log4php/Logger.php');

// Plugin definition
$PluginInfo['Logger'] = array(
	'Description' => 'Logger for Vanilla',
	'Version' => '0.1',
	'RequiredApplications' => array('Vanilla' => '2.0.10'),
	'RequiredTheme' => FALSE,
	'RequiredPlugins' => FALSE,
	'HasLocale' => FALSE,
	'SettingsUrl' => '/plugin/logger/settings',
	'SettingsPermission' => 'Garden.AdminUser.Only',
	'Author' => 'Diego Zanella',
	'AuthorEmail' => 'diego@pathtoenlightenment.net',
	'AuthorUrl' => 'http://dev.pathtoenlightenment.net',
);

class LoggerPlugin extends Gdn_Plugin {
	/**
	 * Set Validation Rules related to Configuration Model.
	 *
	 * @param Gdn_Validation $Validation The Validation that is (or will be)
	 * associated to the Configuration Model.
	 *
	 * @return void
	 */
	protected function _SetConfigModelValidationRules(Gdn_Validation $Validation) {
	}

	/**
	 * Plugin constructor
	 *
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Base_Render_Before Event Hook
	 *
	 * @param $Sender Sending controller instance
	 */
	public function Base_Render_Before(&$Sender) {
		$Sender->AddCssFile($this->GetResource('design/css/logger.css', FALSE, FALSE));
		//$Sender->AddJsFile($this->GetResource('js/logger.js', FALSE, FALSE));
	}

	/**
	 * Create a method called "Logger" on the PluginController
	 *
	 * @param $Sender Sending controller instance
	 */
	public function PluginController_Logger_Create(&$Sender) {
		// Basic plugin properties
		$Sender->Title('Logger Plugin');
		$Sender->AddSideMenu('plugin/logger');

		// Prepare form for sub-pages
		$Sender->Form = new Gdn_Form();

		// Forward the call to the appropriate method.
		$this->Dispatch($Sender, $Sender->RequestArgs);
	}

	public function Controller_Index($Sender) {
		// Prevent non-admins from accessing this page
		$Sender->Permission('Plugins.Logger.Manage');

		// CSS And JavaScript for this specific page
		$Sender->AddJsFile($this->GetResource('js/logger.js', FALSE, FALSE));

		//$Sender->SetData('CurrentPath', LOGGER_VIEWLOG_URL);

		//$Sender->Render($this->GetView('logger_viewlog.php'));
	}

	public function Controller_Settings(&$Sender) {
		// TODO Implement Plugin's Settings page
		return;

		// Prevent non-admins from accessing this page
		$Sender->Permission('Plugins.Logger.Manage');
		$Sender->SetData('CurrentPath', LOGGER_GENERALSETTINGS_URL);

		$Validation = new Gdn_Validation();
		$this->_SetConfigModelValidationRules($Validation);

		$ConfigurationModel = new Gdn_ConfigurationModel($Validation);
		$ConfigurationModel->SetField(array(
		));

		// Set the model on the form.
		$Sender->Form->SetModel($ConfigurationModel);

		// If seeing the form for the first time...
		if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
			// Apply the config settings to the form.
			$Sender->Form->SetData($ConfigurationModel->Data);
		}
		else {
			$Saved = $Sender->Form->Save();
			if ($Saved) {
				$Sender->InformMessage(T('Your changes have been saved.'));
			}
		}

		// TODO Implement management of General Settings
		$Sender->Render($this->GetView('logger_generalsettings_view.php'));
	}

	public static function GetLogger($LoggerName) {
		return Logger::getLogger("main");
	}

	/**
	 * Add a link to the Administrator Dashboard menu
	 *
	 * By grabbing a reference to the current SideMenu object we gain access to its methods, allowing us
	 * to add a menu link to the newly created /plugin/Logger method.
	 *
	 * @param $Sender Sending controller instance
	 */
	public function Base_GetAppSettingsMenuItems_Handler(&$Sender) {
		$Menu = &$Sender->EventArguments['SideMenu'];
		$Menu->AddLink('Add-ons', T('Logger'), 'plugin/logger', 'Garden.AdminUser.Only');
	}

	/**
	 * Plugin setup
	 *
	 * This method is fired only once, immediately after the plugin has been enabled in the /plugins/ screen,
	 * and is a great place to perform one-time setup tasks, such as database structure changes,
	 * addition/modification ofconfig file settings, filesystem changes, etc.
	 */
	public function Setup() {
		// Set up plugin's default values
		// TODO Set up plugin's default values

		// Create Database Objects needed by the Plugin
		// TODO Implement Plugin's Schema class
		//require('install/logger.schema.php');
		//LoggerSchema::Install();
	}

	/**
	 * Cleanup operations to be performend when the Plugin is disabled, but not
	 * permanently removed.
	 */
	public function OnDisable() {
	}

	/**
	 * Plugin cleanup
	 */
	public function CleanUp() {
		//require('install/logger.schema.php');
		//LoggerSchema::Uninstall();
	}

}
