<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2011 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form_old\element\validator\impl;

use DevelSuite\form\element\validator\dsAValidator;

/**
 * Validator for Usernames.
 *
 * @package DevelSuite\form\element\validator\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsUsernameValidator extends dsAValidator {

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

	/**
	 * Set the minimum lenght of the username
	 *
	 * @param int $minLength
	 * 		Minimum lenght of username
	 */
	public function setMinLength($minLength) {
		$this->minLength = $minLength;
	}

	/**
	 * Set the maximum lenght of the username
	 *
	 * @param int $maxLength
	 * 		Maximum lenght of username
	 */
	public function setMaxLength($maxLength) {
		$this->maxLength = $maxLength;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\validator.dsAValidator::validateElement()
	 */
	public function validateElement() {
		$userName = $this->element->getValue();

		$return = TRUE;
		if(strlen($userName) < $this->minLength || strlen($userName) > $this->maxLength) {
			$return = FALSE;
		}

		if(!preg_match("/^[a-z0-9äöüÄÖÜß_\.\-@\s]+$/i", $userName)) {
			$return = FALSE;
		}

		return $return;
	}
}