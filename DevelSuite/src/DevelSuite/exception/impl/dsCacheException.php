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
class dsCacheException extends dsErrorCodeException {
	const CACHE_DIR_NOT_SPECIFIED = 100;
	const CACHE_DIR_INVALID = 200;
	const ERROR_WRITING_DATA = 300;
	const ERROR_READING_DATA = 400;
	const ERROR_DELETING_FILE = 500;

	public function __construct($errorKey, $args = array()) {
		parent::__construct(new dsErrorCode(realpath(dirname(__FILE__)) . DS . "exceptions", "CacheException", $errorKey, $args));
	}
}