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
// Main Log4PHP Library
require(PATH_PLUGINS . '/Logger/lib/external/log4php/Logger.php');

// Plugin definition
$PluginInfo['Logger'] = array(
	'Description' => 'Logger for Vanilla',
	'Version' => '2012.09.05 alpha',
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
	private static $_AppendersManager;
	private static $_LoggerConfigModel;
	private static $_LoggerInitialized = false;

	/**
	 * Returns an instance of AppendersManager. The function follows the principle
	 * of lazy initialization, instantiating the class the first time it's
	 * requested. This method is static because the AppendersManager is required
	 * by a global validation function.
	 *
	 * @return An instance of AppendersManager.
	 */
	public static function AppendersManager() {
		if(empty(self::$_AppendersManager)) {
			// Logger Appenders Manager will be used to keep track of available
			// appenders
			self::$_AppendersManager = new LoggerAppendersManager();
		}

		return self::$_AppendersManager;
	}

	/**
	 * Returns an instance of LoggerConfigModel. The function follows the principle
	 * of lazy initialization, instantiating the class the first time it's
	 * requested. This method is static because the LoggerConfigModel is required
	 * by other static methods.
	 *
	 * @return An instance of LoggerConfigModel.
	 */
	protected function LoggerConfigModel() {
		if(empty(self::$_LoggerConfigModel)) {
			// Logger Appenders Manager will be used to keep track of available
			// appenders
			self::$_LoggerConfigModel = new LoggerConfigModel();
		}

		return self::$_LoggerConfigModel;
	}

	/**
	 * Returns True if Log4php Logger has been initialized with a call to its
	 * ::Config() method, or False if it hasn't been.
	 *
	 * @return An instance of True if Log4php Logger has been initialized with a
	 * call to its ::Config() method, or False if it hasn't been.
	 */
	protected function LoggerInitialized() {
		return self::$_LoggerInitialized;
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
		$Validation->AddRule('ValidAppenderClass', 'function:ValidateAppenderClass');

		// Validation rules for Appender Type
		$Validation->ApplyRule('AppenderClass', 'ValidAppenderClass', T('Appender Type is not valid. Please select an Appender Type from the ones in the list.'));
	}

	/**
	 * Plugin constructor
	 *
	 */
	public function __construct() {
		parent::__construct();

		// Perform initialization steps
		$this->Initialize();
	}

	/**
	 * Performs several initialization steps needed for the plugin to work
	 * correctly. These steps have been moved from method
	 * PluginController_Logger_Create().
	 *
	 * @return void.
	 */
	protected function Initialize() {
		// Load Logger Configuration and use it to initialize Log4php
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
		$Sender->SetData('CurrentPath', LOGGER_APPENDERS_LIST_URL);

		$AppenderConfigModel = new LoggerAppenderConfigModel();
		$AppendersDataSet = $AppenderConfigModel->Get();
		// TODO Handle Limit and Offset

		// TODO Add Pager

		$Sender->SetData('AppendersDataSet', $AppendersDataSet);
		$Sender->Render($this->GetView('logger_appenderslist_view.php'));
	}

	public function Controller_Settings(&$Sender) {
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

		$Sender->Render($this->GetView('logger_generalsettings_view.php'));
	}

	/**
	 * Returns an instance of the Log4php Logger.
	 *
	 * @param LoggerName The name of the Logger to retrieve.
	 * @return An instance of a Log4php Logger.
	 */
	public static function GetLogger($LoggerName = 'system') {
		if(!self::LoggerInitialized()) {
			Logger::configure(self::LoggerConfigModel()->Get());
			self::$_LoggerInitialized = true;
		}

		return Logger::getLogger($LoggerName);
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
													 $FormValues['AppenderClass']));
				}
			}
		}

		$Sender->SetData('AppenderClasses', self::AppendersManager()->GetAppendersLabels());
		$Sender->SetData('AppenderClassesDescriptions', self::AppendersManager()->GetAppendersDescriptions());
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

		// If it's a PostBack, then the AppenderClass has been passed by the form. If
		// not, it will have been passed as an argument with the Request.
		$AppenderClass = $Sender->Form->AuthenticatedPostBack() ? $Sender->Form->GetValue['AppenderClass'] : $Sender->Request->GetValue(LOGGER_ARG_APPENDERTYPE, null);

		// The Appender Type is always required. This is because we are in three
		// possible scenarios when this page is opened:
		// - Page has just been opened for an INSERT. In such case, we need the Appender Type
	  //   to know which View to present to the User and how to validate them.
		// - Page has just been opened for an UPDATE. In such case, we need the Appender Type
	  //   to load the appropriate Model, which will decode the configuration into
		//   the values to populate the View.
		//   Note: an UPDATE request always contains an Appender ID, which could theoretically
		//   be used to retrieve the Appender Type. However, this would require several extra
		//   steps:
		//   - Instantiate a generic LoggerAppenderConfigModel
		//   - Run a query to get the Appender Type
		//   - Instantiate the specific LoggerAppenderConfigModel
		//   - Run another query to retrieve the configuration and decode it into
		//     the various fields.
		//   Therefore, it's simpler to just expect the Appender Type to be passed
		//   with the request.
		// - Page has been opened after the User clicked on Save. In such case, the
		//   Appender Type is supposed to be part of the configuration form.
		//
		// For the above reasons, it's safe to assume that the page can't be opened
		// if an Appender Type hasn't been specified.
		if(empty($AppenderClass)) {
			throw new InvalidArgumentException(sprintf(T('Invalid Request. Argument %s (Appender Type) is required.'), LOGGER_ARG_APPENDERTYPE));
		}

		// Load appropriate Appender Configuration Model, depending on Appender Type
		$AppenderConfigModel = self::AppendersManager()->GetModel($AppenderClass);

		// Set the model on the form.
		$Sender->Form->SetModel($AppenderConfigModel);

		// If seeing the form for the first time...
		if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
			$AppenderID = $Sender->Request->GetValue(LOGGER_ARG_APPENDERID, null);

			if(isset($AppenderID)) {
				// Load the data of the Client to be edited, if an Appender ID is passed
				$AppenderSettings = $AppenderConfigModel->GetAppenderConfig($AppenderID);

				$Sender->Form->SetData($AppenderSettings);
			}
			else {
				// Set only the AppenderClass, if Appender ID is null (i.e. it's an Add New Appender operation)
				$Sender->Form->SetValue('AppenderClass', $AppenderClass);
			}
		}
		else {
			//var_dump($Sender->Form->FormValues());

			// The field named "Save" is actually the Save button. If it exists, it means
			// that the User chose to save the changes.
			$Data = $Sender->Form->FormValues();

			// If User Canceled, go back to the List
			if($Data['Cancel']) {
				Redirect(LOGGER_APPENDERS_LIST_URL);
			}

			if(Gdn::Session()->ValidateTransientKey($Data['TransientKey']) && $Data['Save']) {
				// Save Appender settings
				$Saved = $Sender->Form->Save();

				if ($Saved) {
					$Sender->InformMessage(T('Your changes have been saved.'));
					$this->FireEvent('ConfigChanged');

					// Once changes have been saved, redurect to the main page
					Redirect(LOGGER_APPENDERS_LIST_URL);
				}
			}
		}

		// Add some descriptive data about the Appender
		$Sender->SetData('AppenderInfo', self::AppendersManager()->GetAppenderInfo($AppenderClass));
		// Retrieve the sub-View that will be used to configure the parameters
		// specific to the selected Logger Appender.
		$Sender->Data['AppenderConfigView'] = self::AppendersManager()->GetConfigView($AppenderClass);
		$Sender->Render($this->GetView('loggerappender_edit_config_view.php'));
	}


	public function Controller_TestLog($Sender) {
		$Exception = new Exception(T('This is a test Exception, no action is required.'));
		LoggerPlugin::GetLogger('system')->info(T('This is a test Log message, no action is required.'),
																						$Exception);

		$Sender->InformMessage(T('Test log message issued.'));

		$this->Controller_Index($Sender);
	}

	/**
	 * Renders the page that allows Users to Delete a Log Appender.
	 *
	 * @param Sender The Sender generated by the Request.
	 * @return void.
	 */
	public function Controller_AppenderDelete(&$Sender) {
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
	 * Handles the event LoggerPlugin.ConfigChanged, which is fired whenever
	 * the configuration for the Logger is modified.
	 *
	 * @param Sender The Sender generated by the Request.
	 * @return void.
	 */
	public function LoggerPlugin_ConfigChanged_Handler($Sender) {
		$this->LoggerConfigModel()->RebuildConfiguration();
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
