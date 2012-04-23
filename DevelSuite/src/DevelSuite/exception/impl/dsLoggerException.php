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
class dsLoggerException extends dsErrorCodeException {
	const LOGGER_UNAVAILABLE 				= 100;
	const LAYOUT_UNDEFINED 	 				= 200;
	const APPENDER_UNDEFINED				= 300;
	const CONFIGFILE_EXTENSION_UNSUPPORTED 	= 400;
	const CONFIGFILE_NOT_AVAILABLE 			= 500;
	const LOGLEVEL_MISSING					= 600;
	const LOGLEVEL_UNDEFINED				= 700;
	const LOGFILE_MISSING					= 800;
	const LOGFILE_UNAVAILABLE 				= 900;
	const LOCK_CONFIGFILE_IMPOSSIBLE		= 1000;

	public function __construct($errorKey, $args = array()) {
		parent::__construct(new dsErrorCode(realpath(dirname(__FILE__)) . DS . "exceptions", "LoggerException", $errorKey, $args));
	}
}