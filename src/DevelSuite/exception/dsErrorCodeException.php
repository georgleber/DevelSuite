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
 * FIXME
 *
 * @package DevelSuite\exception
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsErrorCodeException extends \Exception {
	private $errorCode;
	private $messageArgs;

	public function __construct(dsIErrorCode $errorCode, $args = NULL, $code = 0) {
		$this->errorCode = $errorCode;
		$this->messageArgs = $args;

		$iniArr = dsResourceBundle::getBundle($this->errorCode->getFilePath());
		$msg = sprintf($iniArr[$this->errorCode->getSection()][$this->errorCode->getKey()], $args);

		parent::__construct($msg);
	}

	public function getErrorCode() {
		return $this->errorCode;
	}
	public function getMessageArguments() {
		return $this->messageArgs;
	}
}