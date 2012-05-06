<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\serviceprovider;

use DevelSuite\serviceprovider\annotation\Inject;

use DevelSuite\config\dsConfig;
use DevelSuite\reflection\dsReflectionClass;

/**
 * FIXME
 *
 * @package DevelSuite\serviceprovider
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsServiceProvider {
	private $parameterMap;
	private $classMap;

	public function registerParameter($alias, $value) {
		$this->parameterMap[$alias] = $value;
	}

	public function registerService($alias, $class) {
		$class = str_replace(".", "\\", $class);

		$this->classMap[$alias]["class"] = $class;
		$this->classMap[$alias]["injected"] = FALSE;
		$this->classMap[$alias]["instance"] = NULL;

	}

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
				$method->invoke($instance, $parameters);
			}
		}

		return $instance;
	}

	private function determineInstances(array $parameters) {
		$params = array();
		foreach ($parameters as $parameter) {
			$loadClass = $parameter->getClass();
			if (array_key_exists($loadClass, $this->classMap)) {
				if ($this->classMap[$alias]["injected"] == FALSE) {
					$class = $this->classMap[$alias]["class"];
					$instance = $this->loadClass($class);

					$this->classMap[$alias]["instance"] = $instance;
					$this->classMap[$alias]["injected"] = TRUE;
				} else {
					$instance = $this->classMap[$alias]["instance"];
				}

				$params = $instance;
			}
		}
	}
}