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
	'Description' => 'Logger for Vanilla - Basic Version',
	'Version' => '12.10.30-basic',
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
	private static $_LoggerInitialized = false;

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
		Redirect(LOGGER_GENERALSETTINGS_URL);
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
			Logger::configure(PATH_PLUGINS . '/Logger/config.xml');
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
}
