<?php if (!defined('APPLICATION')) exit();

/**
 * This class extends the standard LoggerAutoloader provided with Log4php. Its
 * purpose is to expose the list containing the various Appenders, Filters and
 * Configurators hard-coded in the LoggerAutoloader, which are stored into a
 * private property.
 */
class LoggerAutoloaderWrapper extends LoggerAutoloader {

	/**
	 * Checks if a string (Class name) starts with the supplied pattern.
	 *
	 * @param ClassName The Class Name to be checked.
	 * @param Pattern The pattern to be checked against the Class Name.
	 * @return True if the Class Name starts with the specified pattern, False otherwise.
	 */
	protected static function ClassStartsWith($ClassName, $Pattern) {
		return (strpos($ClassName, $Pattern, 0) === 0);
	}

	/**
	 * Returns a list of all the Appenders whose classes are mapped inside the
	 * LoggerAutoloader.
	 *
	 * @return A list of all the Appenders whose classes are mapped inside the
	 * LoggerAutoloader.
	 */
	public static function GetAppenders() {
		$Result = array();

		$AutoloaderClasses = self::$classes;
		foreach($AutoloaderClasses as $ClassName => $FileName) {
			// The list also contains classes other than Appenders, therefore some
			// basic filtering is required.
			if(self::ClassStartsWith($ClassName, 'LoggerAppender')) {
				$Result[] = $ClassName;
			}
		}
		return $Result;
	}
}
