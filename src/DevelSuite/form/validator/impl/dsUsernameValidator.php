<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2011 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form\validator\impl;

/**
 * Validator for Usernames.
 *
 * @package DevelSuite\form\validator\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsUsernameValidator extends dsPatternValidator {
	/**
	 * Minimum lenght of usernames (default: 3)
	 * @var int
	 */
	private $minLength = 3;

	/**
	 * Maximum lenght of usernames (default: 15)
	 * @var int
	 */
	private $maxLength = 15;

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\validator.dsAValidator::init()
	 */
	protected function init() {
		$this->pattern = "^[a-z0-9äöüÄÖÜß_\.\-@\s]+$";
	}

	/**
	 * Set the minimum lenght of the username
	 *
	 * @param int $minLength
	 * 		Minimum lenght of username
	 */
	public function setMinLength($minLength) {
		$this->minLength = $minLength;
		return $this;
	}

	/**
	 * Set the maximum lenght of the username
	 *
	 * @param int $maxLength
	 * 		Maximum lenght of username
	 */
	public function setMaxLength($maxLength) {
		$this->maxLength = $maxLength;
		return $this;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\validator.dsAValidator::validateElement()
	 */
	public function validateElement() {
		$result = parent::validateElement();

		if ($result) {
			$userName = $this->element->getValue();
			if(strlen($userName) < $this->minLength || strlen($userName) > $this->maxLength) {
				$result = FALSE;
			}
		}

		return $result;
	}
}