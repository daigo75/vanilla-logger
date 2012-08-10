<?php if (!defined('APPLICATION')) exit();

/**
 * Manages the list of all available Appenders and provides convenience
 * functions to retrieve the Model, Validation and View for each one.
 */
class LoggerAppendersManager {
	private $Logger;
	// TODO Extract to an ancestor class all methods and properties that could be reusable for Layout Manager, Filter Manager and so on
	protected $Appenders = array();

	public function Add($AppenderClass, $Label, $Description) {
		$this->Appenders[strtolower($AppenderClass)] = array('Label' => $Label,
																												 'Description' => $Description,);
	}

	/**
	 * Checks if an Appender Class exists in the list of the configured ones.
	 *
	 * @param AppenderClass The Appender class to be checked.
	 * @return True if the class exists in the list of configured Appenders, False otherwise.
	 */
	function AppenderExists($AppenderClass) {
		return array_key_exists(strtolower($AppenderClass), $this->Appenders);
	}

	/**
	 * Builds and returns the name of the Model that handles the configuration
	 * of a Logger Appender class.
	 *
	 * @param AppenderClass The class of Appender for which to retrieve the
	 * Model.
	 * @return The class name of the Model.
	 */
	protected function GetModelClass($AppenderClass) {
		return $this->AppenderExists($AppenderClass) ? sprintf('%sConfigModel', $AppenderClass) : null;
	}

	/**
	 * Builds and returns the name of the Validation that will be used to validate
	 * the configuration for the a Logger Appender.
	 *
	 * @param AppenderClass The class of Appender for which to retrieve the
	 * Model.
	 * @return The class name of the Validation.
	 */
	protected function GetValidationClass($AppenderClass) {
		return $this->AppenderExists($AppenderClass) ? sprintf('%sValidation', $AppenderClass) : null;
	}

	/**
	 * Builds and returns the full name of the View to be used as an interface to
	 * configure Logger Appender.
	 *
	 * @param AppenderClass The class of Appender for which to retrieve the
	 * View.
	 * @return The full path and file name of the View.
	 */
	public function GetConfigView($AppenderClass) {
		if(!$this->AppenderExists($AppenderClass)) {
			return null;
		}

		$ViewName = sprintf('%s_config_view.php', strtolower($AppenderClass));
		return sprintf('%s/appenders/%s', LOGGER_PLUGIN_VIEW_PATH, $ViewName);
	}

	/**
	 * Factory method. It instantiates the appropriate Model and Validation for
	 * the specified Appender, and returns the configured Model.
	 *
	 * @param AppenderClass The class of Appender for which to instantiate the
	 * Model.
	 * @return An instance of the Model to handle the configuration of the
	 * specified Appender Class.
	 * @throws A Logger_AppenderModelException if either the Model or its Validation could not be
	 * instantiated.
	 */
	public function GetModel($AppenderClass) {
		$ModelClass = $this->GetModelClass($AppenderClass);

		// If ModelClass is valid, then it means that the Appender is in the list.
		// Therefore, the Validation just needs to be retrieved.
		if(isset($ModelClass)) {
			$ValidationClass = $this->GetValidationClass($AppenderClass);

			try {
				$Validation = new $ValidationClass();
				// The Validation is passed to the Model to "assemble" a complete model,
				// which will automatically perform appropriate validation of the
				// configuration.
				$Model = new $ModelClass($Validation);

				return $Model;
			}
			catch(Exception $e) {
				// Log the exception to keep track of it, but throw it again afterwards.
				// This is useful in case the person who sees the Exception can't fix it
				// and has to require assistance from a Developer, who might not be
				// readily available.
				$Message = sprintf(T('Exception occurred while instantiating Model for Appender "%s": %s'),
																			$AppenderClass,
																			$e->getMessage());
				$this->Logger->Error($Message);
				throw new Logger_AppenderModelException($Message, null, $e);
			}
		}
	}

	public function __construct() {
		// Get System (root) Logger
		$this->Logger = LoggerPlugin::GetLogger();

		// Add Console Appender
		$this->Add('LoggerAppenderConsole',
							 T('Console'),
							 T('Writes logging events to the <code>php://stdout</code> or the ' .
								 '<code>php://stderr</code> stream, the former being the default target.'));
	}
}
