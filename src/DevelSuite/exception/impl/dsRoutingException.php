<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2011 Georg Henkel <info@develman.de>
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
class dsRoutingException extends dsErrorCodeException {
	const ROUTE_NOT_FOUND = 100;

	public function __construct($errorKey, $args = array()) {
		parent::__construct(new dsErrorCode(realpath(dirname(__FILE__)) . DS . "exceptions", "RoutingException", $errorKey, $args));
	}
}