<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form\validator\impl;

/**
 * Validator for House numbers.
 *
 * @package DevelSuite\form\validator\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsHousenoValidator extends dsPatternValidator {
	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\validator.dsAValidator::init()
	 */
	protected function init() {
		$this->pattern = "^\d+[a-zA-Z]?$";
	}
}