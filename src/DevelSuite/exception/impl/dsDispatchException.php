<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\exception\impl;

use DevelSuite\exception\dsErrorCode;
use DevelSuite\exception\dsErrorCodeException;

/**
 * FIXME
 *
 * @package DevelSuite\exception
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsDispatchException extends dsErrorCodeException {
	const CONTROLLER_INVALID = 100;
	const CONTROLLERCLASS_NOT_FOUND = 200;
	const CONTROLLERFILE_NOT_FOUND = 300;
	const ACTION_UNDEFINED = 400;
	const ACTION_NOT_CALLABLE = 500;
	const ACTION_HAS_WRONG_RESULT = 600;
	const LAYOUT_NOT_FOUND = 700;
	const VIEW_NOT_FOUND = 700;
	const VIEWHELPER_NOT_KNOWN = 800;

	public function __construct($errorKey, $args = array()) {
		parent::__construct(new dsErrorCode(realpath(dirname(__FILE__)) . DS . "exceptions", "DispatchException", $errorKey, $args));
	}
}