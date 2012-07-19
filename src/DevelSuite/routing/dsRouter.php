<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Parts of this file are taken from dannyvankootens PHP-Router.
 * See the PHP-ROUTER_LICENSE file.
 */
namespace DevelSuite\routing;

use DevelSuite\util\dsStringTools;

use DevelSuite\dsApp;
use DevelSuite\exception\impl\dsDispatchException;
use DevelSuite\routing\route\dsRoute;

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

		if (dsStringTools::isFilled($name)) {
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
			throw new dsDispatchException(dsDispatchException::ROUTE_NOT_FOUND, array($requestUri));
		}

		return $route;
	}

	/**
	 * Find a route by the given name
	 *
	 * @param string $name
	 * 		Name of the route
	 * @throws dsDispatchException
	 * 		Will be thrown if no route was found
	 *
	 * @return dsRoute named route element
	 */
	public function findRoute($name) {
		$route = NULL;
		if (isset(self::$namedRoutes[$name])) {
			$route = self::$namedRoutes[$name];
		}

		if ($route == NULL) {
			throw new dsDispatchException(dsDispatchException::NAMED_ROUTE_NOT_FOUND, array($name));
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
	public function generateUrl($name, array $params = array()) {
		$route = $this->findRoute($name);
		$url = $route->getPattern();

		// replace route url with given parameters
		if ($params && preg_match_all("/:(\w+)/", $url, $matches)) {
			// loop trough parameter names, store matching value in $params array
			foreach ($matches[1] as $i => $key) {
				if (isset($params[$key])) {
					$url = preg_replace("/:(\w+)/", $params[$key], $url, 1);
				}
			}
		}

		return $url;
	}
}