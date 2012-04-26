<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite;

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
	private static $request;
	private static $response;
	private static $route;
	private static $router;
	private static $viewHelperCache;

	public static function init($config, $routing) {
		self::initConfiguration($config);
		self::initRouting($routing);
		// self::initLogging();
	}

	private static function initConfiguration($config) {
		if (!defined('APP_PATH')) {
			echo "FEHLER: APP_PATH not set";
			exit;
		}

		// configuration
		require_once($config);
		dsConfig::write('core.version', 'DevelSuite 1.0.0');

		$locale = dsConfig::read("app.locale");
		self::setupIniValues($locale);

		// init session handling
		dsSession::configure();
	}

	private static function initRouting($routing) {
		// bind standard controller for startpage
		dsRouter::bind("/", array("controller" => "home"), array(), "home");

		// bind all defined routes
		require_once($routing);

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

	private static function setupIniValues($locale) {
		// set default timezone
		date_default_timezone_set('Europe/Berlin');

		setlocale(LC_ALL, $locale->getLanguage() . "_" . $locale->getCountry() . ".utf-8");

		// maximale Ausfuehrungszeit des Scripts wenn moeglich erhoehen
		if (function_exists("set_time_limit") == TRUE && @ini_get("safe_mode") == 0) {
			@set_time_limit(300);
		}
	}
}