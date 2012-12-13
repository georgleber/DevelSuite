<?php
/*
 * This file is part of the DevelSuite
* Copyright (C) 2012 Georg Henkel <info@develman.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
namespace DevelSuite\view;

use DevelSuite\dsApp;
use DevelSuite\exception\impl\dsRenderingException;

/**
 * Abstract super class for Views
 *
 * @package DevelSuite\view
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
abstract class dsAView implements dsIView {
	/**
	 * Assigned values, accessable from within the template
	 * @var array
	 */
	protected $data = array();

	/**
	 * Assign values to the view
	 *
	 * @param string $key
	 * 			Key of the Value
	 * @param mixed $value
	 * 			The value to assign
	*/
	public function assign($key, $value) {
		$this->data[$key] = $value;
		return $this;
	}

	/**
	 * Retrieves a value from the assigned values
	 *
	 * @param string $key
	 * 		Key under which the value is saved
	 */
	public function __get($key) {
		return $this->data[$key];
	}

	/**
	 * Is used to call an action of a ViewHelper.
	 *
	 * @param string $method
	 * 		(Short-)Name of the ViewHelper
	 * @param array $arguments
	 * 		First argument is the action the rest are the
	 * 		arguments needed by the action
	 */
	public function __call($method, $arguments) {
		$viewHelper = dsApp::getViewHelperCache()->lookup($method);

		// first argument is action name
		$action = $arguments[0];
		$params = $arguments[1];

		$result = NULL;
		if (method_exists($viewHelper, $action) && is_callable(array($viewHelper, $action))) {
			$result = call_user_func_array(array($viewHelper, $action), (array)$params);
		} else {
			throw new dsRenderingException(dsRenderingException::ACTION_NOT_CALLABLE, array($action,  get_class($viewHelper)));
		}

		return $result;
	}
}