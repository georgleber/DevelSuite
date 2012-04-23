<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2011 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\view;

use DevelSuite\controller\dsPageController;

use DevelSuite\dsApp;

use DevelSuite\exception\impl\dsDispatchException;

use DevelSuite\view\helper\dsIViewHelper;

/**
 * Abstract super class for Views
 *
 * @package DevelSuite\view
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
abstract class dsAView {
	/**
	 * Assigned values, accessable from within the template
	 * @var array
	 */
	private $values = array();

	/**
	 * Load a ViewHelper
	 *
	 * @param string $helperName
	 * 		Name of the ViewHelper
	 */
	public function getHelper($helperName) {
		return dsApp::getViewHelperCache()->lookup($helperName);
	}

	/**
	 * Assign values to the view
	 *
	 * @param string $key
	 * 			Key of the Value
	 * @param mixed $value
	 * 			The value to assign
	 */
	public function assign($key, $value) {
		$this->values[$key] = $value;
		return $this;
	}

	/**
	 * Retrieves a value from the assigned values
	 *
	 * @param string $key
	 * 		Key under which the value is saved
	 */
	public function __get($key) {
		return $this->values[$key];
	}

	/**
	 * Renders the view
	 */
	abstract public function render();
}