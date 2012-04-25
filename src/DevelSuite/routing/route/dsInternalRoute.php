<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\routing\route;

/**
 * InteralRoute is used of internal routing.
 * It handles the internal target patterns to resolve module,
 * controller and action.
 *
 * @package DevelSuite\routing\route
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsInternalRoute extends dsARoute {
	/**
	 * Constructor
	 *
	 * @param array $params
	 */
	public function __construct(array $params = array()) {
		$this->parameters = $params;
	}

	/**
	 * (non-PHPdoc)
	 * @see DevelSuite\routing.dsARoute::parse()
	 */
	public function parse($target) {
		$module = NULL;
		$ctrl = NULL;
		$action = NULL;

		// check if an action is set in the string and extract it
		if(strpos($target, "::") !== FALSE) {
			list($ctrl, $action) = explode("::", $target);
		} else {
			$ctrl = $target;
			$action = "index";
		}

		// extract controller and module
		if(strpos($ctrl, "/") !== FALSE) {
			list($module, $ctrl) = explode("/", $ctrl);
		}

		$this->module = $module;
		$this->controller = $ctrl;
		$this->action = $action;
	}
}