<?php if (!defined('APPLICATION')) exit();
/**
{licence}
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
	'Version' => '12.10.30',
	'RequiredApplications' => array('Vanilla' => '2.0.10'),
	'RequiredTheme' => FALSE,
	'RequiredPlugins' => FALSE,
	'HasLocale' => FALSE,
	'MobileFriendly' => TRUE,
	'SettingsUrl' => '/plugin/logger/settings',
	'SettingsPermission' => 'Garden.AdminUser.Only',
	'Author' => 'Diego Zanella',
	'AuthorEmail' => 'diego@pathtoenlightenment.net',
	'AuthorUrl' => 'http://dev.pathtoenlightenment.net',
	'RegisterPermissions' => array('Plugins.Logger.Manage',
																 'Plugins.Logger.ViewLog',),
);

/**
 * Implements the Logger Plugin for Vanilla Forums. This plugin leverages Log4php
 * and allows to log messages to several Appenders.
 */
class LoggerPlugin extends Gdn_Plugin {
	private static $_AppendersManager;
	private static $_LoggerConfigModel;
	private static $_AppenderConfigModel;
	private static $_LoggerInitialized = false;
	private $_SysDBLogModel;

	/**
	 * Returns an instance of AppendersManager. The function follows the principle
	 * of lazy initialization, instantiating the class the first time it's
	 * requested. This method is static because the AppendersManager is required
	 * by a global validation function.
	 *
	 * @return object An instance of AppendersManager.
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
	 * @return object An instance of LoggerConfigModel.
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
	 * Returns an instance of AppenderConfigModel. The function follows the principle
	 * of lazy initialization, instantiating the class the first time it's
	 * requested. This method is static because the AppenderConfigModel is required
	 * by other static methods.
	 *
	 * @return object An instance of AppenderConfigModel.
	 */
	protected function AppenderConfigModel() {
		if(empty(self::$_AppenderConfigModel)) {
			// Logger Appenders Manager will be used to keep track of available
			// appenders
			self::$_AppenderConfigModel = new LoggerAppenderConfigModel();
		}

		return self::$_AppenderConfigModel;
	}

	/**
	 * Returns an instance of VanillaDBLogModel, which will query SysDBLogModel
	 * table. The function follows the principle of lazy initialization,
	 * instantiating the class the first time it's requested. This method is
	 * static because the VanillaDBLogModel is required by other static methods.
	 *
	 * @return object An instance of VanillaDBLogModel.
	 */
	protected function GetSysDBLogModel() {
		if(empty($this->_SysDBLogModel)) {
			// Logger Appenders Manager will be used to keep track of available
			// appenders
			$this->_SysDBLogModel = new VanillaDBLogModel('LoggerSysLog');
		}

		return $this->_SysDBLogModel;
	}

	/**
	 * Returns True if Log4php Logger has been initialized with a call to its
	 * ::Config() method, or False if it hasn't been.
	 *
	 * @return object An instance of True if Log4php Logger has been initialized with a
	 * call to its ::Config() method, or False if it hasn't been.
	 */
	protected function LoggerInitialized() {
		return self::$_LoggerInitialized;
	}

	/**
	 * Set Validation Rules related to Configuration Model.
	 *
	 * @param object Gdn_Validation $Validation The Validation that is (or will be)
	 * associated to the Configuration Model.
	 *
	 * @return void
	 */
	protected function _SetConfigModelValidationRules(Gdn_Validation $Validation) {
		$Validation->ApplyRule('Plugin.Logger.LogLevel', 'Required', T('Please specify a Logging Level.'));
	}

	/**
	 * Set Validation Rules related to VanillaDBLog Model.
	 *
	 * @param object Gdn_Validation $Validation The Validation that is (or will be)
	 * associated to the Configuration Model.
	 *
	 * @return void
	 */
	protected function _SetLogViewValidationRules(Gdn_Validation $Validation) {
	}

	/**
	 * Set Validation Rules related to the step of adding a new Appender.
	 *
	 * @param object Gdn_Validation $Validation The Validation that is (or will be)
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
	}

	/**
	 * Base_Render_Before Event Hook
	 *
	 * @param object Sender Sending controller instance
	 */
	public function Base_Render_Before(&$Sender) {
		$Sender->AddCssFile($this->GetResource('design/css/logger.css', FALSE, FALSE));
		//$Sender->AddJsFile($this->GetResource('js/logger.js', FALSE, FALSE));
	}

	/**
	 * Create a method called "Logger" on the PluginController
	 *
	 * @param object Sender Sending controller instance
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

	/**
	 * Renders the Plugin's default (index) page.
	 *
	 * @param object Sender Sending controller instance.
	 */
	public function Controller_Index($Sender) {
		Redirect(LOGGER_VIEW_LOG_URL);
	}

	/**
	 * Renders the page where Users can see the logged messages.
	 *
	 * @param object Sender Sending controller instance.
	 */
	public function Controller_ViewLog($Sender) {
		// Prevent non-admins from accessing this page
		$Sender->Permission('Plugins.Logger.ViewLog');

		// CSS And JavaScript for this specific page
		//$Sender->AddJsFile($this->GetResource('js/logger.js', FALSE, FALSE));
		$Sender->SetData('CurrentPath', LOGGER_VIEW_LOG_URL);

		// If seeing the form for the first time...
		if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
			// Just show the form with the default values

			// Default DateFrom is today
			$Sender->Form->SetFormValue('DateFrom', date('Y-m-d'));
			// Default DateTo is today
			$Sender->Form->SetFormValue('DateTo', date('Y-m-d'));
		}
		else {
			// Else, validate submitted data.
			$Validation = new Gdn_Validation();
			$this->_SetLogViewValidationRules($Validation);

			$FormValues = $Sender->Form->FormValues();

			$Validation->Validate($FormValues);
			$Sender->Form->SetValidationResults($Validation->Results());

			if(!$Sender->Form->ErrorCount()){
				$DateFrom = $Sender->Form->GetFormValue('DateFrom');
				$DateTo = $Sender->Form->GetFormValue('DateTo');

				$LogDataSet = $this->GetSysDBLogModel()->Get($DateFrom, $DateTo)->Result();

				$Sender->SetData('LogDataSet', $LogDataSet);
			}
		}
		// TODO Handle Limit and Offset
		// TODO Add Pager

		$Sender->Render($this->GetView('logger_viewlog_view.php'));
	}

	/**
	 * Renders the page showing the list of configured Appenders.
	 *
	 * @param object Sender Sending controller instance
	 */
	public function Controller_Appenders($Sender) {
		// Prevent non-admins from accessing this page
		$Sender->Permission('Plugins.Logger.Manage');

		$Sender->SetData('CurrentPath', LOGGER_APPENDERS_LIST_URL);

		$AppenderConfigModel = $this->AppenderConfigModel();
		$AppendersDataSet = $AppenderConfigModel->Get();
		// TODO Handle Limit and Offset

		// TODO Add Pager

		$Sender->SetData('AppendersDataSet', $AppendersDataSet);
		$Sender->Render($this->GetView('logger_appenderslist_view.php'));
	}

	/**
	 * Renders the Settings page.
	 *
	 * @param object Sender Sending controller instance
	 */
	public function Controller_Settings(&$Sender) {
		// Prevent non-admins from accessing this page
		$Sender->Permission('Plugins.Logger.Manage');
		$Sender->SetData('CurrentPath', LOGGER_GENERALSETTINGS_URL);

		$Validation = new Gdn_Validation();
		$this->_SetConfigModelValidationRules($Validation);

		$ConfigurationModel = new Gdn_ConfigurationModel($Validation);
		$ConfigurationModel->SetField(array(
			'Plugin.Logger.LogLevel' => LoggerLevel::INFO,
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
				$this->FireEvent('ConfigChanged');
				$Sender->InformMessage(T('Your changes have been saved.'));
			}
		}

		$Sender->Render($this->GetView('logger_generalsettings_view.php'));
	}

	/**
	 * Returns an instance of the Log4php Logger.
	 *
	 * @param string LoggerName The name of the Logger to retrieve.
	 * @return object An instance of a Log4php Logger.
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
	 * @param object Sender Sending controller instance
	 */
	public function Base_GetAppSettingsMenuItems_Handler(&$Sender) {
		$Menu = &$Sender->EventArguments['SideMenu'];
		$Menu->AddLink('Add-ons', T('Logger'), 'plugin/logger', 'Garden.AdminUser.Only');
	}

	/**
	 * Renders the page that allows Users to Add a Log Appender. This is the first
	 * step of the Add process, where User simply chooses the type of Appender.
	 *
	 * @param object Sender The Sender generated by the Request.
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
	 * @param object Sender The Sender generated by the Request.
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
				// Load the data of the Appender to be edited, if an Appender ID is passed
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


	/**
	 * Enables/disables a Log Appender.
	 *
	 * @param object Sender The Sender generated by the Request.
	 * @return void.
	 */
	public function Controller_AppenderEnable(&$Sender) {
		// Prevent Users without proper permissions from accessing this page.
		$Sender->Permission('Plugins.Logger.Manage');

		$AppenderID = $Sender->Request->GetValue(LOGGER_ARG_APPENDERID, null);
		$EnableFlag = $Sender->Request->GetValue(LOGGER_ARG_ENABLEFLAG, null);

		if(is_numeric($AppenderID) && is_numeric($EnableFlag)) {
			$AppenderConfigModel = $this->AppenderConfigModel();

			if($AppenderConfigModel->EnableAppender((int)$AppenderID, (int)$EnableFlag)) {
				$Sender->InformMessage(T('Your changes have been saved.'));
				$this->FireEvent('ConfigChanged');
			};
		}

		Redirect(LOGGER_APPENDERS_LIST_URL);
	}


	/**
	 * Issues one or more Test Log Messages to all the configured loggers, and
	 * indicates how much time it took to do it. It's useful to verify that all
	 * loggers work as expected, and to evaluate the performance of each.
	 *
	 * @param object Sender The request Sender.
	 * @return void.
	 */
	public function Controller_TestLog($Sender) {
		$AmountOfLogMessages = isset($Sender->RequestArgs[1]) ? $Sender->RequestArgs[1] : 1;

		$LogStart = time();

		// Issues the amount of Log Messages specified as an argument. The code is
		// willingly inefficient, creating an Exception and an error message every
		// time, because it simulates what would happen in real use cases.
		for($MessageIdx = 0; $MessageIdx < $AmountOfLogMessages; $MessageIdx++) {
			$Exception = new Exception(T('This is a test Exception, no action is required.'));
			LoggerPlugin::GetLogger('system')->info(T('This is a test Log message, no action is required.'),
																							$Exception);
		}

		$LogEnd = time();
		$ElapsedTime = $LogEnd - $LogStart;

		$Sender->InformMessage(sprintf(T('%d test log message(s) issued in %s'),
																	 $AmountOfLogMessages,
																	 gmdate('H:i:s', $ElapsedTime)));

		Redirect(LOGGER_APPENDERS_LIST_URL);
	}

	/**
	 * Renders the page that allows Users to Delete a Log Appender.
	 *
	 * @param object Sender The Sender generated by the Request.
	 */
	public function Controller_AppenderDelete(&$Sender) {
		// Prevent Users without proper permissions from accessing this page.
		$Sender->Permission('Plugins.Logger.Manage');

		$AppenderConfigModel = $this->AppenderConfigModel();

		$Sender->Form->SetModel(&$AppenderConfigModel);

		// If seeing the form for the first time...
		if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
			$AppenderID = $Sender->Request->GetValue(LOGGER_ARG_APPENDERID, null);

			// Load the data of the Appender to be edited, if an Appender ID is passed
			$AppenderSettings = $AppenderConfigModel->GetAppenderConfig($AppenderID);

			$Sender->Form->SetData($AppenderSettings);

			// Apply the config settings to the form.
			$Sender->Render($this->GetView('loggerappender_delete_confirm_view.php'));
		}
		else {
			//var_dump($Sender->Form->FormValues());
			$Data = $Sender->Form->FormValues();

			//var_dump($Data);

			// The field named "OK" is actually the OK button. If it exists, it means
			// that the User confirmed the deletion.
			if(Gdn::Session()->ValidateTransientKey($Data['TransientKey']) && $Data['OK']) {
				// Delete Client Id
				$AppenderConfigModel->Delete($Data['AppenderID']);

				$Sender->InformMessage(T('Appender deleted.'));
			}
			Redirect(LOGGER_APPENDERS_LIST_URL);
		}
	}

	/**
	 * Handles the event LoggerPlugin.ConfigChanged, which is fired whenever
	 * the configuration for the Logger is modified.
	 *
	 * @param object Sender The Sender generated by the Request.
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
		SaveToConfig('Plugin.Logger.LogLevel', LoggerLevel::INFO);

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
