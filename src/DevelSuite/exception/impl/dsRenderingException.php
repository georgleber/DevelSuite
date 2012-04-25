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
 * Signals that an exception during layouting / rendering
 * has occured.
 *
 * @package DevelSuite\exception
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsRenderingException extends dsErrorCodeException {
	const ID = "RenderingException";

	const LAYOUT_NOT_FOUND  		= 100;
	const TEMPLATE_NOT_FOUND		= 200;
	const VIEWHELPER_NOT_REGISTERED	= 300;
	const VIEWHELPER_NOT_FOUND 		= 400;
	const VIEWHELPER_INVALID		= 500;
	const ACTION_NOT_CALLABLE  		= 600;

	/**
	 * Constructor
	 *
	 * @param int $errorKey
	 * 		Key of the error
	 * @param array $args
	 * 		Array with arguments to set in the error message
	 * @param Exception $prevException
	 * 		Cause for this exception
	 */
	public function __construct($errorKey, array $args = array(), $prevException = NULL) {
		$path = dirname(__FILE__);

		parent::__construct(new dsErrorCode($path, self::ID, $errorKey), $args, $prevException);
	}
}