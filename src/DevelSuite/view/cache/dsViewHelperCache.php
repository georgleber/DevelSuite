<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\view\cache;

use DevelSuite\exception\impl\dsDispatchException;
use DevelSuite\view\helper\dsIViewHelper;

/**
 * Cache for lazy loading ViewHelpers
 *
 * @package DevelSuite\view\cache
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsViewHelperCache {
	/**
	 * Cache for all ViewHelper definitions
	 * @var array
	 */
	private $viewHelper = array();

	/**
	 * Cache for the initialized ViewHelper classes
	 * @var array
	 */
	private $classCache = array();

	/**
	 * Add a ViewHelper definition to the cache
	 *
	 * @param string $className
	 * 		Name of the class
	 * @param string $namespace
	 * 		Namespace of that class
	 * @param string $shortName
	 * 		Short name to lookup in cache
	 */
	public function addViewHelper($className, $namespace = NULL, $shortName = NULL) {
		if (!isset($shortName)) {
			$shortName = $className;
		}

		$this->viewHelper[$shortName] = array($namespace, $className);
	}

	/**
	 * Lookup a ViewHelper in the cache and if not initialized already, initialize and
	 * save it into the class cache.
	 *
	 * @param string $helperName
	 * @throws dsDispatchException
	 */
	public function lookup($helperName) {
		if (!array_key_exists($helperName, $this->viewHelper)) {
			throw new dsDispatchException(dsDispatchException::VIEWHELPER_NOT_KNOWN);
		}

		if (!array_key_exists($helperName, $this->classCache)) {
			$viewHelper = $this->viewHelper[$helperName];

			if ($viewHelper[0] != NULL) {
				$this->classCache[$helperName] = new $viewHelper[0] . DIRECTORY_SEPARATOR . $viewHelper[0];
			} else {
				$class = "DevelSuite\\view\\helper\\" . $viewHelper[1];
				$viewHelperClass = new $class();

				if ($viewHelperClass instanceof dsIViewHelper) {
					$this->classCache[$helperName] = $viewHelperClass;
				} else {
					// FIXME!
					throw new dsDispatchException(dsDispatchException::CONTROLLER_INVALID);
				}
			}
		}

		return $this->classCache[$helperName];
	}
}