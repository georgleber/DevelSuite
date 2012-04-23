<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2011 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\core\form\element\validator\impl;

use DevelSuite\core\form\element\validator\dsAValidator;

/**
 * Validator for zip codes.
 *
 * @package DevelSuite\core\form\element\validator\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsZipCodeValidator extends dsAValidator {
	/* (non-PHPdoc)
	 * @see DevelSuite\core\form\element\validator.dsAValidator::validate()
	 */
	public function validateElement() {
		$value = $this->element->getValue();

		// do some zip code relevant checks

		return TRUE;
	}
}