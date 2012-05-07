<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\serviceprovider;

use DevelSuite\config\dsConfig;
use DevelSuite\reflection\dsReflectionClass;
use DevelSuite\serviceprovider\annotation\Inject;

/**
 * FIXME
 * ServiceProvider is a dependency injection container, which can load 
 * services via constructor injection or setter injection. 
 *
 * @package DevelSuite\serviceprovider
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsServiceProvider {
	/**
	 * Map with all registered services
	 * @var array
	 */
	private $classMap = array();

	/**
	 * Register a service to the provider
	 *
	 * @param string $alias
	 * 		Alias name of the service
	 * @param string $class
	 * 		Classname of the service (NAMESPACE: use . instead of \)
	 */
	public function registerService($alias, $class) {
		$class = str_replace(".", "\\", $class);

		$this->classMap[$alias]["class"] = $class;
		$this->classMap[$alias]["injected"] = FALSE;
		$this->classMap[$alias]["instance"] = NULL;
	}

	/**
	 * Load a service
	 *
	 * @param string $alias
	 * 		Alias name of the service
	 */
	public function get($alias) {
		$instance = NULL;
		if (array_key_exists($alias, $this->classMap)) {
			if ($this->classMap[$alias]["injected"] == FALSE) {
				$class = $this->classMap[$alias]["class"];
				$instance = $this->loadClass($class);

				$this->classMap[$alias]["instance"] = $instance;
				$this->classMap[$alias]["injected"] = TRUE;
			} else {
				$instance = $this->classMap[$alias]["instance"];
			}
		}

		return $instance;
	}

	/**
	 * Loads the class and injects all parameters
	 *
	 * @param string $className
	 * 		Name of the class
	 */
	private function loadClass($className) {
		// load class via reflection and parse methods for @Inject
		$reflClass = new dsReflectionClass($className);

		// check for constructor injection
		$constParams = array();
		$constructor = $reflClass->getConstructor();
		if ($constructor->getNumberOfParameters() > 0) {
			// load parameters
			$constParams = $this->determineInstances($method->getParameters());
		}
		// instantiate class
		$instance = $reflClass->newInstanceArgs($constParams);

		// check only public methods
		$reflMethods = $reflClass->getAnnotatedMethods(Inject::NAME);
		foreach ($reflMethods as $method) {
			if ($method->getNumberOfParameters() > 0) {
				// load parameters
				$parameters = $this->determineInstances($method->getParameters());
				// invoke method
				$method->invokeArgs($instance, $parameters);
			}
		}

		return $instance;
	}

	/**
	 * Parse and load class instances of the parameters
	 *
	 * @param array $parameters
	 * 		Parameters to parse
	 */
	private function determineInstances(array $parameters) {
		$params = array();
		foreach ($parameters as $parameter) {
			$loadClass = NULL;

			// ReflectionParameter::getClass() throws an exception if a class can not be autoloaded
			// If this happens we catch the exception and parse the message to retrieve the classname
			try {
				$loadClass = $parameter->getClass()->getName();
			} catch (\ReflectionException $e) {
				if (preg_match('#Class (.+) does not exist#', $e->getMessage(), $m)) {
					$loadClass = $m[1];
				}
			}

			if ($loadClass != NULL) {
				// load class from classmap
				if (($alias = $this->findClass($loadClass)) != NULL) {
					// if it is not already loaded inject it
					if ($this->classMap[$alias]["injected"] == FALSE) {
						$class = $this->classMap[$alias]["class"];
						$instance = $this->loadClass($class);

						$this->classMap[$alias]["instance"] = $instance;
						$this->classMap[$alias]["injected"] = TRUE;
					} else {
						$instance = $this->classMap[$alias]["instance"];
					}

					$params[] = $instance;
				}
			} else {
				// FIXME throw exception that parameter could not be parsed
			}
		}

		return $params;
	}

	/**
	 * Searches for a className in the classMap
	 *
	 * @param string $loadClass
	 * 		Class to load from classMap
	 */
	private function findClass($loadClass) {
		$keys = array_keys($this->classMap);
		foreach ($keys as $key) {
			if ($this->classMap[$key]['class'] === $loadClass) {
				return $key;
			}
		}

		return NULL;
	}
}