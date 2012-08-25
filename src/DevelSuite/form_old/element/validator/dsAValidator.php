<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2011 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form_old\element\validator;

use DevelSuite\expression\dsAExpression;
use DevelSuite\form\element\dsAElement;

/**
 * Abstract superclass for all form element validators.
 *
 * @package DevelSuite\form\element\validator
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
abstract class dsAValidator {
	protected $element;
	protected $expression;
	protected $errorMessage;

	/**
	 * Class constructor
	 *
	 * @param string $errorMessage
	 * 			ErrorMessage, which will be shown if element is not valid
	 */
	public function __construct(dsAElement $element, $errorMessage = NULL) {
		$this->element = $element;

		if (isset($errorMessage)) {
			$this->errorMessage = $errorMessage;
		}

		$this->init();
	}

	/**
	 * Replacement of the constructor in order to configure the controller before calling an action
	 */
	protected function init() {}

	/**
	 * Set an expression element to validate the value
	 *
	 * @param dsAExpression $expression
	 * 			Expression for comparison of the element
	 */
	public function setExpression(dsAExpression $expression) {
		$this->expression = $expression;
	}

	/**
	 * @return $errorMessage
	 * 			The error message for this validation
	 */
	public function getErrorMessage() {
		return $this->errorMessage;
	}

	/**
	 * Validate the element for errors
	 *
	 * @return TRUE if element is valid
	 */
	public function validate() {
		$result = $this->validateElement();

		if (!$result) {
			$this->element->setErrorMessage($this->errorMessage);
		}

		return $result;
	}

	/**
	 * Validates the element for correctness.
	 */
	abstract public function validateElement();
}