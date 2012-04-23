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
class dsAuthorizationException extends dsErrorCodeException {
	const USERNAME_ALREADY_EXISTS = 100;
	const EMAIL_ALREADY_EXISTS = 200;

	public function __construct($errorKey, $args = array()) {
		parent::__construct(new dsErrorCode(realpath(dirname(__FILE__)) . DS . "exceptions", "AuthorizationException", $errorKey, $args));
	}
}