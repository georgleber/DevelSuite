<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2011 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form\element\validator\impl;

use DevelSuite\form\element\validator\dsAValidator;

/**
 * Validator for Usernames.
 *
 * @package DevelSuite\form\element\validator\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsUsernameValidator extends dsAValidator {
	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\element\validator.dsAValidator::validateElement()
	 */
	public function validateElement() {
		$userName = $this->element->getValue();

		$return = TRUE;
		if(strlen($userName) < 3 || strlen($userName) > 15) {
			$return = FALSE;
		}

		if(!preg_match("/^[a-z0-9äöüÄÖÜß_\.\-@\s]+$/i", $userName)) {
			$return = FALSE;
		}

		return $return;
	}
}