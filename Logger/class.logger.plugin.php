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
require(LOGGER_PLUGIN_LIB_PATH . '/logger.validation.php');

require(PATH_PLUGINS . '/Logger/lib/external/log4php/Logger.php');

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
	 * Builds the full name of the View that handles the configuration of a
	 * specific LoggerAppender.
	 *
	 * @param LoggerAppenderType The type of Appender for which to retrieve the
	 * View.
	 * @return The full path and file name of the View used to configure the
	 * Appender.
	 */
	protected function GetAppenderConfigView($LoggerAppenderType) {
		$ViewName = sprintf('%s_config_view.php', $LoggerAppenderType);
		return sprintf('%s/appenders/%s', LOGGER_PLUGIN_VIEW_PATH, $ViewName);
	}

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
	 * Set Validation Rules related to the step of adding a new Appender.
	 *
	 * @param Gdn_Validation $Validation The Validation that is (or will be)
	 * associated to the Configuration Model.
	 *
	 * @return void
	 */
	protected function _SetAppenderAddValidationRules(Gdn_Validation $Validation) {
		$Validation->AddRule('ValidAppenderType', 'function:ValidateAppenderType');

		// Validation rules for Appender Type
		$Validation->ApplyRule('AppenderType', 'ValidAppenderType', T('Appender Type is not valid. Please select an Appender Type from the ones in the list.'));
	}

	/**
	 * Plugin constructor
	 *
	 */
	public function __construct() {
		parent::__construct();

		//Logger::configure(PATH_PLUGINS . '/Logger/testconfig.xml');
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
		$logger = Logger::getLogger('root');
		$logger->info("foo");

		// Prevent non-admins from accessing this page
		$Sender->Permission('Plugins.Logger.Manage');

		// CSS And JavaScript for this specific page
		$Sender->AddJsFile($this->GetResource('js/logger.js', FALSE, FALSE));

		$Sender->SetData('CurrentPath', LOGGER_APPENDERS_LIST_URL);

		$Sender->Render($this->GetView('logger_appenderslist_view.php'));
	}

	public function Controller_Settings(&$Sender) {
		// TODO Implement Plugin's Settings page
		throw new Exception('Not implemented');

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
	 * Renders the page that allows Users to Add a Log Appender. This is the first
	 * step of the Add process, where User simply chooses the type of Appender.
	 *
	 * @param Sender The Sender generated by the Request.
	 * @return void.
	 */
	public function Controller_AppenderAdd(&$Sender) {
		// Prevent Users without proper permissions from accessing this page.
		$Sender->Permission('Plugins.Logger.Manage');

		// If seeing the form for the first time...
		if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
			// Nothing to do in this case
		}
		else {
			//var_dump($Sender->Form->FormValues());
			$Data = $Sender->Form->FormValues();
			// The field named "Cancel" is the Cancel button. By clicking it, User
			// will return to the Appenders List page.
			if($Data['Cancel']) {
				Redirect(LOGGER_APPENDERS_LIST_URL);
				return;
			}

			// The field named "Next" is the Next button. If it exists, it means
			// that the User chose to save the choice and proceed with the creation of
			// an Appender.
			if(Gdn::Session()->ValidateTransientKey($Data['TransientKey']) && $Data['Next']) {
				$Validation = new Gdn_Validation();
				$this->_SetAppenderAddValidationRules($Validation);

				// Validate form data
				$FormValues = $Sender->Form->FormValues();
				$Validation->Validate($FormValues);

				$Sender->Form->SetValidationResults($Validation->Results());

				// If data is valid, redirect to the page where the User can complete the
				// configuration of the Appender.
				if(!$Sender->Form->ErrorCount()){
					Redirect(sprintf('%s?%s=%s',
													 LOGGER_APPENDER_EDIT_URL,
													 LOGGER_ARG_APPENDERTYPE,
													 $FormValues['AppenderType']));
				}
			}
		}

		$Sender->SetData('AppenderTypes', LoggerConst::GetAppenderTypes());
		$Sender->SetData('AppenderTypesDescriptions', LoggerConst::GetAppenderTypesDescriptions());
		$Sender->Render($this->GetView('logger_appender_add_view.php'));
	}

	/**
	 * Renders the page that allows Users to Add/Edit a Log Appender.
	 *
	 * @param Sender The Sender generated by the Request.
	 * @return void.
	 */
	public function Controller_AppenderEdit(&$Sender) {
		// Prevent Users without proper permissions from accessing this page.
		$Sender->Permission('Plugins.Logger.Manage');

		// TODO Implement function Appender Edit
		//throw new Exception('Not implemented');

		$AppenderType = $Sender->Request->GetValue(LOGGER_ARG_APPENDERTYPE, null);

		// Load appropriate Appender Configuration Model, depending on Appender Type
		// TODO Evaluate the possibility of using Gdn::Factory to install and load Appender Configuration Model class.
		$AppenderConfigClass = sprintf('%sConfigModel', $AppenderType);
		$AppenderConfigModel = &new $AppenderConfigClass();

		// Set the model on the form.
		$Sender->Form->SetModel(&$AppenderConfigModel);

		// If seeing the form for the first time...
		if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
			$AppenderID = $Sender->Request->GetValue(LOGGER_ARG_APPENDERID, null);

			if(isset($AppenderID)) {
				// Load the data of the Client to be edited, if an Appender ID is passed
				$AppenderSettings = $AppenderConfigModel->GetAppenderSettings($AppenderID);
				$Sender->Form->SetData($AppenderSettings);
			}
		}
		else {
			//var_dump($Sender->Form->FormValues());

			// TODO Implement validation for specific Appender

			// If requested, generate a User ID and a Secret Key
			//if($Sender->Form->GetFormValue(LOGGER_ARG_GENERATE_ID) || $Sender->Request->Post(LOGGER_ARG_GENERATE_ID)) {
			//	$Sender->Form->SetFormValue('ClientID', mt_rand());
			//	$Sender->Form->SetFormValue('SecretKey', sha1(uniqid('', TRUE)));
			//}
			//else {
			//	// The field named "Save" is actually the Save button. If it exists, it means
			//	// that the User chose to save the changes.
			//	$Data = $Sender->Form->FormValues();
			//	if(Gdn::Session()->ValidateTransientKey($Data['TransientKey']) && $Data['Save']) {
			//		// Save settings
			//		$Saved = $Sender->Form->Save();
			//
			//		if ($Saved) {
			//			$Sender->InformMessage(T('Your changes have been saved.'));
			//		}
			//	}
			//
			//	// Whether User Saved or Canceled, return to Client List page.
			//	//Redirect(LOGGER_APPENDER_LIST_URL);
			//	$this->Controller_Index($Sender);
			//}
		}

		$Sender->Render($this->GetAppenderConfigView($AppenderType));
	}

	/**
	 * Renders the page that allows Users to Delete a Log Appender.
	 *
	 * @param Sender The Sender generated by the Request.
	 * @return void.
	 *
	 */
	public function Controller_Delete(&$Sender) {
		// Prevent Users without proper permissions from accessing this page.
		$Sender->Permission('Plugins.Logger.Manage');

		// TODO Implement function Appender-Delete
		throw new Exception('Not implemented');

		//$APIClientsModel = new LoggerAPIClientsModel();
		//$Sender->Form->SetModel(&$APIClientsModel);
		//
		//// If seeing the form for the first time...
		//if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
		//	$ClientID = $Sender->Request->GetValue(LOGGER_ARG_APPENDERID, null);
		//
		//	// Load the data of the Client to be edited, if a Client ID is passed
		//	$ClientSettings = $APIClientsModel->GetClientSettings($ClientID);
		//	$Sender->Form->SetData($ClientSettings);
		//
		//	// Apply the config settings to the form.
		//	$Sender->Render($this->GetView('logger_client_delete_confirm_view.php'));
		//}
		//else {
		//	//var_dump($Sender->Form->FormValues());
		//	$Data = $Sender->Form->FormValues();
		//
		//		var_dump($Data);
		//
		//	// The field named "OK" is actually the OK button. If it exists, it means
		//	// that the User confirmed the deletion.
		//	if(Gdn::Session()->ValidateTransientKey($Data['TransientKey']) && $Data['OK']) {
		//		// Delete Client Id
		//		$APIClientsModel->Delete($Data ['ClientID']);
		//
		//		$Sender->InformMessage(T('Client deleted.'));
		//	}
		//	Redirect(LOGGER_APPENDER_LIST_URL);
		//}
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
		require('install/logger.schema.php');
		LoggerSchema::Install();
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
		require('install/logger.schema.php');
		LoggerSchema::Uninstall();
	}

}
