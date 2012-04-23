<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\routing;

use DevelSuite\dsApp;
use DevelSuite\exception\impl\dsRoutingException;
use DevelSuite\routing\dsRoute;

/**
 * The router handles all incoming request and
 * forwards it to the correct destination of module/controller/action.
 *
 * @package DevelSuite\routing
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsRouter {
	/**
	 * List of all routes
	 * @var array
	 */
	private static $routes = array();

	/**
	 * List of all named routes
	 * @var array
	 */
	private static $namedRoutes = array();

	/**
	 * Bind a route to a given URI pattern for SEF linking.
	 *
	 * @param string $routePattern
	 * 		Pattern of the route
	 * @param array $definedValues
	 * 		Predefined values for the route
	 * @param array $constraints
	 * 		Constraints of parameters
	 * @param array $methods
	 * 		Allowed methods for the route
	 */
	public static function bind($routePattern, array $definedValues, array $constraints = array(), $name = NULL, array $methods = array()) {
		$route = new dsRoute($routePattern, $definedValues, $constraints);

		if (!empty($methods)) {
			$route->setMethods($methods);
		}

		if ($name != NULL) {
			self::$namedRoutes[$name] = $route;
		}

		self::$routes[] = $route;
	}

	/**
	 * Matches the current request against a mapped route. 
	 * 
	 * @return dsRoute route if found otherwise FALSE
	 */
	public function matchRequest() {
		$request = dsApp::getRequest();

		$requestMethod = $request->getRequestMethod();
		// strip GET variables from URL
		$requestUri = $request->getHeader('request_uri');
		if (($pos = strpos($requestUri, '?')) !== FALSE) {
			$requestUri = substr($requestUri, 0, $pos);
		}

		if (substr($requestUri, -1) !== '/') {
			$requestUri .= '/';
		}

		$route = NULL;
		foreach (self::$routes as $usedRoute) {
			// compare server request method with route's allowed http methods
			if (!in_array($requestMethod, $usedRoute->getMethods())) {
				continue;
			}

			if (($route = $usedRoute->parse($requestUri)) !== FALSE) {
				break;
			}
		}

		if ($route == NULL) {
			throw new dsRoutingException(dsRoutingException::ROUTE_NOT_FOUND);
		}

		return $route;
	}

	/**
	 * Retrieves a route by the given name and creates a URL of it
	 *
	 * @param string $routeName
	 * 		Name of the route
	 * @param array $params
	 * 		Possible parameters of the URL
	 *
	 * @return string URL of the named route
	 */
	public static function generateUrl($name, array $params = array()) {
		if (!isset(self::$namedRoutes[$name])) {
			throw new dsRoutingException(dsRoutingException::ROUTE_NOT_FOUND);
		}


		$route = self::$namedRoutes[$name];
		$url = $route->getPattern();

		// replace route url with given parameters
		if ($params && preg_match_all("/:(\w+)/", $url, $matches)) {
			// loop trough parameter names, store matching value in $params array
			foreach ($matches as $i => $key) {
				if (isset($params[$key])) {
					$url = preg_replace("/:(\w+)/", $params[$key], $url, 1);
				}
			}
		}

		return $url;
	}
}