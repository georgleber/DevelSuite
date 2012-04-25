<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\view\cache;

use DevelSuite\config\dsConfig;

use DevelSuite\exception\impl\dsRenderingException;
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
	 * Array for all ViewHelper
	 * @var array
	 */
	private $viewHelper = array();

	/**
	 * Cache for the initialized ViewHelper classes
	 * @var array
	 */
	private $classCache = array();

	/**
	 * Add a ViewHelper to the helper array
	 *
	 * @param string $className
	 * 		Name of the class
	 * @param string $shortName
	 * 		Short name to lookup in cache
	 */
	public function addViewHelper($className, $shortName = NULL) {
		if (!isset($shortName)) {
			$shortName = $className;
		}

		$this->viewHelper[$shortName] = $className;
	}

	/**
	 * Lookup a ViewHelper in the cache and if not initialized already, initialize and
	 * save it into the class cache.
	 *
	 * @param string $helperName
	 *			name of the viewhelper
	 * @throws dsDispatchException
	 */
	public function lookup($helperName) {
		if (!array_key_exists($helperName, $this->viewHelper)) {
			throw new dsRenderingException(dsRenderingException::VIEWHELPER_NOT_REGISTERED, array($helperName));
		}

		if (!array_key_exists($helperName, $this->classCache)) {
			$viewHelper = $this->viewHelper[$helperName];
			$appClass = "\\view\\helper\\" . $viewHelper;
			$frameworkClass = "\\DevelSuite\\view\\helper\\" . $viewHelper;

			// load class from application path if exists otherwise from framework
			$helperClazz = $appClass;
			if (!class_exists($helperClazz, FALSE)) {
				$helperClazz = $frameworkClass;
				if (!class_exists($helperClazz)) {
					throw new dsRenderingException(dsRenderingException::VIEWHELPER_NOT_FOUND, array($helperClazz));
				}
			}

			$viewHelperClass = new $helperClazz();
			if ($viewHelperClass instanceof dsIViewHelper) {
				$this->classCache[$helperName] = $viewHelperClass;
			} else {
				throw new dsRenderingException(dsRenderingException::VIEWHELPER_INVALID, array(get_class($viewHelperClass)));
			}
		}

		return $this->classCache[$helperName];
	}
}