<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form\validator\impl;

use DevelSuite\form\validator\dsAValidator;

/**
 * Validator for age input elements.
 *
 * @package DevelSuite\form\validator\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsAgeValidator extends dsAValidator {
	/**
	 * Minimum age for validation
	 * @var int
	 */
	private $minAge;

	/**
	 * Maximum age for validation
	 * @var int
	 */
	private $maxAge;

	/**
	 * Set the minimum age for validation
	 *
	 * @param int $minAge
	 * 		Minimum age
	 */
	public function setMinAge($minAge) {
		$this->minAge = $minAge;
		return $this;
	}

	/**
	 * Set the maximum age for validation
	 *
	 * @param int $maxAge
	 * 		Maximum age
	 */
	public function setMaxAge($maxAge) {
		$this->maxAge = $maxAge;
		return $this;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\validator.dsAValidator::validateElement()
	 */
	public function validateElement() {
		$value = $this->element->getValue();

		$result = TRUE;
		if (!is_numeric($value)) {
			$result = FALSE;
		}


		if (isset($this->minAge) && $value < $this->minAge) {
			$result = FALSE;
		}

		if (isset($this->maxAge) && $value > $this->maxAge) {
			$result = FALSE;
		}

		return $result;
	}
}