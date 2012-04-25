<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\exception;

use DevelSuite\exception\dsIErrorCode;
use DevelSuite\i18n\dsResourceBundle;

/**
 * ErrorCodeExceptions allow to define a error message for
 * the exceptions which is referenced by a constant.
 *
 * @package DevelSuite\exception
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsErrorCodeException extends \Exception {
	/**
	 * Code of error message to throw
	 * @var dsIErrorCode
	 */
	private $errorCode;

	/**
	 * Arguments used in the error message
	 * @var array
	 */
	private $messageArgs;

	/**
	 * Constructor
	 *
	 * @param dsIErrorCode $errorCode
	 * 			Error code for the message
	 * @param array $args
	 * 			Arguments which can be set in the error message
	 * @param Exception $previousException
	 * 			Cause of this exception
	 */
	public function __construct(dsIErrorCode $errorCode, array $args = array(), $prevException = NULL) {
		$this->errorCode = $errorCode;
		$this->messageArgs = $args;

		// load message from the bundle
		$iniArr = dsResourceBundle::getBundle($this->errorCode->getFilePath(), $this->errorCode->getBundleName());
		$msg = vsprintf($iniArr[$this->errorCode->getKey()], $args);

		parent::__construct($msg, NULL, $prevException);
	}

	/**
	 * Return the arguments of the error message
	 */
	public function getMessageArguments() {
		return $this->messageArgs;
	}
}