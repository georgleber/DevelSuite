<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form\validator;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

use DevelSuite\form\element\dsAElement;

/**
 * Abstract superclass for all form element validators.
 *
 * @package DevelSuite\form\validator
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
abstract class dsAValidator {
	/**
	 * Logger instance
	 * @var Logger
	 */
	protected $log;

	/**
	 * Element to validate
	 * @var dsAElement
	 */
	protected $element;

	/**
	 * Error message which will be set, if validation fails
	 * @var string
	 */
	protected $errorMessage;

	/**
	 * Constructor
	 *
	 * @param dsAElement $element
	 * 		The element to validate
	 * @param string $errorMessage
	 *		ErrorMessage, which will be shown if element is not valid
	 */
	public function __construct(dsAElement $element, $errorMessage = NULL) {
		$this->log = new Logger("AValidator");
		$this->log->pushHandler(new StreamHandler(LOG_PATH . DS . 'server.log'));

		$this->element = $element;

		if (isset($errorMessage)) {
			$this->errorMessage = $errorMessage;
		}

		$this->init();
	}

	/**
	 * Can be used to initialize further information
	 */
	protected function init() {}

	/**
	 * Validate the element for errors
	 *
	 * @return TRUE if element is valid
	 */
	public function validate() {
		$result = $this->validateElement();

		if ($result === FALSE) {
			$this->log->debug("Validation is FALSE, setting error message: " . $this->errorMessage);
			$this->element->setErrorMessage($this->errorMessage);
		}

		return $result;
	}

	/**
	 * Validates the element for correctness.
	 */
	abstract public function validateElement();
}