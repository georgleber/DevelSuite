<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\view\helper\cache;

use DevelSuite\config\dsConfig;
use DevelSuite\exception\impl\dsRenderingException;
use DevelSuite\view\helper\dsIViewHelper;

/**
 * Cache for lazy loading ViewHelpers.
 *
 * @package DevelSuite\view\cache
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsViewHelperCache {
	/**
	 * Cache for the initialized ViewHelper classes
	 * @var array
	 */
	private $classCache = array();

	/**
	 * Lookup a ViewHelper in the cache and if not initialized already, initialize and
	 * save it into the class cache. The lookup will first check the view helper directory (APP_PATH/view/helper) for
	 * the requested ViewHelper. If no is found it will check the path in framework
	 * (DevelSuite/view/helper) for the requested ViewHelper.
	 * The name of a ViewHelper is specified as follows:
	 * <ul>
	 * 	<li>starting with a uppercase letter</li>
	 *  <li>ending with ViewHelper</li>
	 * </ul>
	 *
	 * For example:
	 * You want to lookup the LinkViewHelper, the $helperName just needs to be link.
	 * The exactly class name will be generate from it.
	 * First try: view\helper\LinkViewHelper
	 * if it does not extist: DevelSuite\view\helper\dsLinkViewHelper
	 *
	 * @param string $helperName
	 *			Name of the viewhelper
	 * @throws dsDispatchException
	 */
	public function lookup($helperName) {
		if (!array_key_exists($helperName, $this->classCache)) {
			// load class from application path if exists otherwise from framework
			$helperClazz = "\\view\\helper\\" . ucfirst($helperName) . "ViewHelper";
			if (!class_exists($helperClazz)) {
				$helperClazz = "\\DevelSuite\\view\\helper\\impl\\ds" .  ucfirst($helperName) . "ViewHelper";
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