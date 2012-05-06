<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite;

use DevelSuite\reflection\annotations\dsAnnotationRegistry;

use DevelSuite\session\dsASessionHandler;

use DevelSuite\i18n\dsLocale;

use DevelSuite\eventbus\impl\dsEventBus;

use Monolog\Handler\SyslogHandler;

use Monolog\Processor\WebProcessor;

use Monolog\Handler\StreamHandler;

use Monolog\Logger;

use DevelSuite\routing\dsRoute;

use DevelSuite\routing\dsRouter;

use DevelSuite\config\dsConfig;
use DevelSuite\http\dsResponse;
use DevelSuite\http\dsRequest;
use DevelSuite\session\dsSession;
use DevelSuite\view\cache\dsViewHelperCache;

/**
 * FIXME
 *
 * @package DevelSuite
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsApp {
	const ENV_DEVELOPMENT 	= "DEVELOPMENT";
	const ENV_TEST 			= "TEST";
	const ENV_PRODUCTION	= "PRODUCTION";

	private static $request;
	private static $response;
	private static $route;
	private static $router;
	private static $viewHelperCache;
	private static $logger;
	private static $eventbus;

	/**
	 * Bootstrapping the application
	 */
	public static function init() {
		// system wide constants
		self::initConstants();

		self::initAnnotations();

		// configuration
		self::initConfiguration();

		// logging
		self::initLogging();

		// setup all system settings
		self::initSystem();

		// session management
		# self::initSession();

		// routing
		self::initRouting();

		// publish new state system.boot.complete
		self::getEventBus()->publish("system.boot.complete");
	}

	/**
	 * Initialize system wide constants
	 */
	private static function initConstants() {
		// define CORE_VERION
		define('CORE_VERSION', 'DevelSuite 1.0.0');

		// set DOCUMENT_ROOT
		if (!defined('DOCUMENT_ROOT')) {
			define('DOCUMENT_ROOT', getenv("DOCUMENT_ROOT"));
		}

		// set DS as alias for DIRECTORY_SEPARATOR
		if (!defined('DS')) {
			define('DS', DIRECTORY_SEPARATOR);
		}

		// define APP_PATH
		if (!defined('APP_PATH')) {
			define('APP_PATH', DOCUMENT_ROOT . DS . 'application');

		}

		// define LOG_PATH
		if (!defined('LOG_PATH')) {
			define('LOG_PATH', DOCUMENT_ROOT . DS . 'tmp' . DS . 'log');
		}

		// define CONFIG_PATH
		if (!defined('CONFIG_PATH')) {
			define('CONFIG_PATH', APP_PATH . DS . 'config');
		}
	}

	/**
	 * Initialize system and user annotations
	 */
	private static function initAnnotations() {
		dsAnnotationRegistry::addNamespace("DevelSuite\\reflection\\annotations\\annotation");
		dsAnnotationRegistry::addNamespace("DevelSuite\\form\\annotation");

		// load user annotations saved in annotations.php
		$configFile = CONFIG_PATH . DS . "annotations.php";

		// check that config file exists
		if (file_exists($configFile)) {
			require_once ($configFile);
		}

		print_r($namespaces);
		// the namespaces are saved in a array called $namespaces
		foreach ($namespaces as $namespace) {
			echo "NAMESPACE: " . $namespace . "<br/>";
			dsAnnotationRegistry::addNamespace($namespace);
		}
	}

	/**
	 * Initialize configuration
	 */
	private static function initConfiguration() {
		$configFile = CONFIG_PATH . DS . "config.php";

		// check that config file exists
		if (!file_exists($configFile)) {
			throw new \Exception("Configuration file not found: " . $configFile);
		}

		require_once($configFile);
	}

	/**
	 * Initialize logging
	 */
	private static function initLogging() {
		// FIXME
		// set_error_handler('_exception_handler');

		// FIXME:
		// check LOG_PATH is writeable
		$loggingConf = dsConfig::read("logging");
		print_r($loggingConf);

	}

	/**
	 * Initialize system wide settings
	 *
	 * @throws \Exception
	 */
	private static function initSystem() {
		// check PHP version
		if (version_compare(PHP_VERSION, '5.3.0', '<')) {
			throw new \Exception("At least PHP-Version 5.3.0 is required");
		}

		// increase maximimum execution time (if possible)
		if (function_exists('set_time_limit') == TRUE && @ini_get('safe_mode') == 0) {
			@set_time_limit(300);
		}

		// set system wide locale
		$locale = dsConfig::read("app.locale", dsLocale::$UNITED_KINGDOM);
		setlocale(LC_ALL, $locale->getLanguage() . "_" . $locale->getCountry() . ".utf-8");

		// set default timezone
		$timezone = dsConfig::read("app.timezone", "Europe/Dublin");
		date_default_timezone_set($timezone);

		// set up display_errors and error_reporting depending on environment
		$env = dsConfig::read('app.environment', self::ENV_DEVELOPMENT);
		switch ($env) {
			case self::ENV_DEVELOPMENT:
			case self::ENV_TEST:
				ini_set('display_errors',1);
				ini_set('display_startup_errors',1);
				error_reporting(E_ALL);
				break;
					
			case self::ENV_PRODUCTION:
				ini_set("display_errors", 0);
				ini_set("display_startup_errors", 0);
				error_reporting(0);
				break;
		}
	}

	/**
	 * Initialise session management
	 */
	private static function initSession() {
		$settings = dsConfig::read("session");

		switch ($settings['handler']) {
			case 'file':
				$handler = "DevelSuite\\session\\impl\\dsFileSessionHandler";
				break;

			case 'databse':
				$handler = "DevelSuite\\session\\impl\\dsDatabaseSessionHandler";
				break;

			case 'cache':
				$handler = "DevelSuite\\session\\impl\\dsCacheSessionHandler";
				throw \Exception("Cache session handler is not implemented.");
				break;

			case 'userdefined':
				$handler = $settings['handler_class'];
				break;
		}

		if (!class_exists($handler)) {
			throw \Exception("Session handler could not be found.");
		}

		$sessionHandler = new $handler();
		if (!($sessionHandler instanceof dsASessionHandler)) {
			throw \Exception("Session handler must be subclass of dsASessionHandler.");
		}


	}

	/**
	 * Initialise routing
	 */
	private static function initRouting() {
		$routingFile = CONFIG_PATH . DS . "routing.php";

		// check that routing file exists
		if (!file_exists($routingFile)) {
			throw new \Exception("Routing file not found");
		}

		// bind standard controller for default start page
		dsRouter::bind("/", array("controller" => "home"), array(), "home");

		// bind all defined routes
		require_once($routingFile);

		// unspecific route for all other not defined routes
		dsRouter::bind("/:controller/:action", array("action" => ""));
	}

	public static function getRequest() {
		if (self::$request == NULL) {
			self::$request = new dsRequest();
		}

		return self::$request;
	}

	public static function getResponse() {
		if (self::$response == NULL) {
			self::$response = new dsResponse();
		}

		return self::$response;
	}

	public static function getViewHelperCache() {
		if (self::$viewHelperCache == NULL) {
			self::$viewHelperCache = new dsViewHelperCache();
		}

		return self::$viewHelperCache;
	}

	public static function getRouter() {
		if (self::$router == NULL) {
			self::$router = new dsRouter();
		}

		return self::$router;
	}

	public static function getRoute() {
		if (self::$route == NULL) {
			self::$route = self::getRouter()->matchRequest();
		}

		return self::$route;
	}

	public static function getLogger() {
		if (self::$logger == NULL) {
			self::$logger = new Logger("ControllerLogger");
			self::$logger->pushHandler(new StreamHandler(dirname(APP_PATH) . '/tmp/log/develsuite.log', Logger::DEBUG));
			self::$logger->pushProcessor(new WebProcessor());
		}

		return self::$logger;
	}

	public static function getEventBus() {
		if (self::$eventbus == NULL) {
			self::$eventbus = new dsEventBus();
		}

		return self::$eventbus;
	}
}