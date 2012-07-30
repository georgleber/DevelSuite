<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Parts of this file are taken from CakePHP.
 * See the CAKE_LICENSE file.
 */
namespace DevelSuite\routing\route;

use DevelSuite\util\dsStringTools;

/**
 * Route defines a route for an URI in the system.
 * It contains at least a pattern. A request method can be defined and also
 * contraints for matching and replacing substitutions of the URI can be used.
 *
 * @package DevelSuite\routing\route
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsRoute extends dsARoute {
	/**
	 * Pattern for this route
	 * @var string
	 */
	protected $pattern;

	/**
	 * Defined values for this Route
	 * @var array
	 */
	private $definedValues = array();

	/**
	 * Filters for parameters
	 * @var array
	 */
	private $constraints = array();

	/**
	 * Acceptable request methods (predefined = GET)
	 * @var array
	 */
	private $methods = array('GET');

	/**
	 * Regular expression of the route
	 * @var string
	 */
	private $compiledRoute = NULL;

	/**
	 * Named Segments in the route pattern
	 * @var array
	 */
	private $keys = array();

	/**
	 * Constructor
	 *
	 * @param string $pattern
	 * 		Pattern for this route
	 * @param array $definedValues
	 * 		Predefined values of this route
	 * @param array $constraints
	 * 		Contraints for the parameters
	 */
	public function __construct($pattern, array $definedValues, array $constraints = array(), $routeName = NULL) {
		$this->pattern = $pattern;
		$this->definedValues = $definedValues;
		$this->constraints = $constraints;
		$this->name = $routeName;
	}

	/**
	 * Returns the pattern of this route
	 */
	public function getPattern() {
		return $this->pattern;
	}

	/**
	 * Returns defined methods for this route
	 */
	public function getMethods() {
		return $this->methods;
	}

	/**
	 * Sets the possible methods for this route
	 *
	 * @param array $methods
	 * 		Acceptable methods for this route
	 */
	public function setMethods(array $methods) {
		$this->methods = $methods;
	}

	/**
	 * (non-PHPdoc)
	 * @see DevelSuite\routing.dsARoute::parse()
	 */
	public function parse($target) {
		if (!dsStringTools::isFilled($this->compiledRoute)) {
			$this->compile();
		}

		if (!preg_match($this->compiledRoute, $target, $route)) {
			return FALSE;
		}

		array_shift($route);
		for ($i = 0, $count = count($this->keys); $i <= $count; $i++) {
			unset($route[$i]);
		}

		// Assign controller, action and parameters
		foreach ($this->definedValues as $key => $value) {
			if ($key == "controller") {
				$this->controller = $value;
			} elseif ($key == "action") {
				$this->action = $value;
			} elseif ($key == "module") {
				$this->module = $value;
			} else {
				if (isset($this->parameters[$key])) {
					continue;
				}

				$this->parameters[$key] = $value;
			}
		}

		// check all named segments
		foreach ($this->keys as $key) {
			// if segment is defined in route, check if the controller or action needs to be set
			// otherwise set it as parameter
			if (isset($route[$key])) {
				if ($key == "controller" && dsStringTools::isNullOrEmpty($this->controller)) {
					$this->controller = rawurldecode($route[$key]);
				} else if ($key == "action" && dsStringTools::isNullOrEmpty($this->action)) {
					$this->action = rawurldecode($route[$key]);
				} else if ($key == "module" && dsStringTools::isNullOrEmpty($this->module)) {
					$this->module = rawurldecode($route[$key]);
				} else {
					$this->parameters[$key] = rawurldecode($route[$key]);
				}
			}
		}

		// check rest of parameters in the parsed route
		if (isset($route['_args_'])) {
			$args = explode('/', $route['_args_']);
			foreach ($args as $param) {
				if (empty($param) && $param !== '0' && $param !== 0) {
					continue;
				}
					
				$this->parameters[] = rawurldecode($param);
			}
		}

		return $this;
	}

	/**
	 * Compiles this route to a regular expression to check matching against URIs
	 */
	private function compile() {
		if (!dsStringTools::isFilled($this->compiledRoute)) {
			// compile route
			$elems = array();
			$routeParams = array();
			$parsed = preg_quote($this->pattern, '#');

			// extract named parameters
			preg_match_all('#:([\w-]+)#', $this->pattern, $namedElements);
			foreach ($namedElements[1] as $index => $elem) {
				// save name of the named parameter with leading :
				$search = '\\' . $namedElements[0][$index];
				// does a constraint for this parameter exist?
				if (isset($this->constraints[$elem])) {
					$constraint = NULL;
					// does a predefined value exist?
					if (array_key_exists($elem, $this->definedValues)) {
						$constraint = '?';
					}

					// find the corresponding constraint and replace the param with its constraint
					$slashParam = '/\\' . $namedElements[0][$index];
					if (strpos($parsed, $slashParam) !== FALSE) {
						$routeParams[$slashParam] = '(?:/(?P<' . $elem . '>' . $this->constraints[$elem] . ')' . $constraint. ')' . $constraint;
					} else {
						$routeParams[$search] = '(?:(?P<' . $elem . '>' . $this->constraints[$elem] . ')' . $constraint . ')' . $constraint;
					}
				} else {
					$constraint = NULL;

					// does a predefined value exist?
					if (array_key_exists($elem, $this->definedValues)) {
						$constraint = '?';
					}

					$routeParams[$search] = '(?:(?P<' . $elem . '>[^/]+))' . $constraint;
				}
				$elems[] = $elem;
			}

			// if route pattern ends with a /* then all args are allowed
			if (preg_match('#\/\*$#', $this->pattern)) {
				$parsed = preg_replace('#/\\\\\*$#', '(?:/(?P<_args_>.*))?', $parsed);
			}

			// replace route parameters with its values
			$parsed = str_replace(array_keys($routeParams), array_values($routeParams), $parsed);
			// create a valid regular expression
			$this->compiledRoute = '#^' . $parsed . '[/]*$#';
			$this->keys = $elems;

			//remove definedValues that are also keys. They can cause match failures
			foreach ($elems as $elem) {
				unset($this->definedValues[$elem]);
			}
		}
	}
}