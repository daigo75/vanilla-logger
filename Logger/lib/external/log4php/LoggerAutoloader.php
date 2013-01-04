<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements. See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 *
 *		http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package log4php
 */

// PHP 5.3+ supports the prepending of an AutoLoader
if(phpversion() >= '5.3') {
	if (function_exists('__autoload')) {
		trigger_error("log4php: It looks like your code is using an __autoload() function. log4php uses spl_autoload_register() which will bypass your __autoload() function and may break autoloading.", E_USER_WARNING);
	}

	// IMPORTANT - Changed for integration with Vanilla Forums.
	// The Autoload function MUST be prepended to the existing list of Autoloaders, or
	// Vanilla's Autoloader will kick in, attempting to load the classes needed by Log4php,
	// causing serious performance issues.
	spl_autoload_register(array('LoggerAutoloader', 'autoload'), true, true);
}
else {
	// PHP 5.2 and earlier don't support correctly the prepending of an Autoloader,
	// therefore classes are loaded upfront
	LoggerAutoloader::LoadAllClasses();
}

/**
 * Class autoloader.
 *
 * @package log4php
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version $Revision$
 */
class LoggerAutoloader {

	/** Maps classnames to files containing the class. */
	protected static $classes = array(

		// Base
		'LoggerConfigurable' => '/LoggerConfigurable.php',
		'LoggerAppender' => '/LoggerAppender.php',
		'LoggerAppenderPool' => '/LoggerAppenderPool.php',
		'LoggerConfigurator' => '/LoggerConfigurator.php',
		'LoggerException' => '/LoggerException.php',
		'LoggerHierarchy' => '/LoggerHierarchy.php',
		'LoggerLevel' => '/LoggerLevel.php',
		'LoggerLocationInfo' => '/LoggerLocationInfo.php',
		'LoggerLoggingEvent' => '/LoggerLoggingEvent.php',
		'LoggerMDC' => '/LoggerMDC.php',
		'LoggerNDC' => '/LoggerNDC.php',
		'LoggerLayout' => '/LoggerLayout.php',
		'LoggerReflectionUtils' => '/LoggerReflectionUtils.php',
		'LoggerRoot' => '/LoggerRoot.php',
		'LoggerThrowableInformation' => '/LoggerThrowableInformation.php',

		// Appenders
		'LoggerAppenderFile' => '/appenders/LoggerAppenderFile.php',
		'LoggerAppenderConsole' => '/appenders/LoggerAppenderConsole.php',
		'LoggerAppenderDailyFile' => '/appenders/LoggerAppenderDailyFile.php',
		'LoggerAppenderEcho' => '/appenders/LoggerAppenderEcho.php',
		'LoggerAppenderMail' => '/appenders/LoggerAppenderMail.php',
		'LoggerAppenderMailEvent' => '/appenders/LoggerAppenderMailEvent.php',
		'LoggerAppenderMongoDB' => '/appenders/LoggerAppenderMongoDB.php',
		'LoggerAppenderNull' => '/appenders/LoggerAppenderNull.php',
		'LoggerAppenderPDO' => '/appenders/LoggerAppenderPDO.php',
		'LoggerAppenderPhp' => '/appenders/LoggerAppenderPhp.php',
		'LoggerAppenderRollingFile' => '/appenders/LoggerAppenderRollingFile.php',
		'LoggerAppenderSocket' => '/appenders/LoggerAppenderSocket.php',
		'LoggerAppenderSyslog' => '/appenders/LoggerAppenderSyslog.php',

		// Configurators
		'LoggerConfigurationAdapter' => '/configurators/LoggerConfigurationAdapter.php',
		'LoggerConfigurationAdapterINI' => '/configurators/LoggerConfigurationAdapterINI.php',
		'LoggerConfigurationAdapterPHP' => '/configurators/LoggerConfigurationAdapterPHP.php',
		'LoggerConfigurationAdapterXML' => '/configurators/LoggerConfigurationAdapterXML.php',
		'LoggerConfiguratorDefault' => '/configurators/LoggerConfiguratorDefault.php',

		// Filters
		'LoggerFilter' => '/LoggerFilter.php',
		'LoggerFilterDenyAll' => '/filters/LoggerFilterDenyAll.php',
		'LoggerFilterLevelMatch' => '/filters/LoggerFilterLevelMatch.php',
		'LoggerFilterLevelRange' => '/filters/LoggerFilterLevelRange.php',
		'LoggerFilterStringMatch' => '/filters/LoggerFilterStringMatch.php',

		// Helpers
		'LoggerFormattingInfo' => '/helpers/LoggerFormattingInfo.php',
		'LoggerOptionConverter' => '/helpers/LoggerOptionConverter.php',
		'LoggerPatternParser' => '/helpers/LoggerPatternParser.php',

		// Converters
		'LoggerPatternConverter' => '/helpers/LoggerPatternConverter.php',
		'LoggerBasicPatternConverter' => '/helpers/LoggerBasicPatternConverter.php',
		'LoggerNamedPatternConverter' => '/helpers/LoggerNamedPatternConverter.php',
		'LoggerCategoryPatternConverter' => '/helpers/LoggerCategoryPatternConverter.php',
		'LoggerClassNamePatternConverter' => '/helpers/LoggerClassNamePatternConverter.php',
		'LoggerDatePatternConverter' => '/helpers/LoggerDatePatternConverter.php',
		'LoggerLiteralPatternConverter' => '/helpers/LoggerLiteralPatternConverter.php',
		'LoggerLocationPatternConverter' => '/helpers/LoggerLocationPatternConverter.php',
		'LoggerMDCPatternConverter' => '/helpers/LoggerMDCPatternConverter.php',

		// Layouts
		'LoggerLayoutHtml' => '/layouts/LoggerLayoutHtml.php',
		'LoggerLayoutPattern' => '/layouts/LoggerLayoutPattern.php',
		'LoggerLayoutSerialized' => '/layouts/LoggerLayoutSerialized.php',
		'LoggerLayoutSimple' => '/layouts/LoggerLayoutSimple.php',
		'LoggerLayoutTTCC' => '/layouts/LoggerLayoutTTCC.php',
		'LoggerLayoutXml' => '/layouts/LoggerLayoutXml.php',

		// Renderers
		'LoggerRendererObject' => '/renderers/LoggerRendererObject.php',
		'LoggerRendererDefault' => '/renderers/LoggerRendererDefault.php',
		'LoggerRendererException' => '/renderers/LoggerRendererException.php',
		'LoggerRendererMap' => '/renderers/LoggerRendererMap.php',
	);

	/**
	 * Loads a class.
	 * @param string $className The name of the class to load.
	 */
	public static function autoload($className) {
		if(isset(self::$classes[$className])) {
			require dirname(__FILE__) . self::$classes[$className];
		}
	}

	/**
	 * Loads all classes in one shot.
	 *
	 * This method has been added for backward
	 * compatibility with PHP 5.2, which doesn't support correctly the "prepend"
	 * parameter for the spl_autoloader(). Simply removing such parameter would
	 * allow the autoloader to run correctly, but it would bring a cost in terms
	 * of performances, as Vanilla Autoloader would kick in first and perform a
	 * file scan to look for the classes, which it would not be able to find, as
	 * they don't follow Vanilla's naming convention.
	 *
	 * To prevent this from happening, on PHP 5.2 all Log4php classes are loaded
	 * upfront. It carries a cost in terms of resources used, but it improves
	 * performances.
	 */
	public static function LoadAllClasses() {
		foreach(self::$classes as $ClassName => $ClassFile) {
			require dirname(__FILE__) . $ClassFile;
		}
	}
}
