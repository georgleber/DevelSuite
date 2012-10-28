<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form\validator\impl;

use DevelSuite\util\dsStringTools;

use DevelSuite\form\validator\dsAValidator;

/**
 * Validator used for validating input elements against a regular expression
 *
 * @package DevelSuite\form\validator\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsPatternValidator extends dsAValidator {
	/**
	 * Pattern for validation
	 * @var string
	 */
	protected $pattern;

	/**
	 * Set the pattern for validation
	 *
	 * @param string $pattern
	 * 		Regular expression
	 */
	public function setPattern($pattern) {
		$this->pattern = $pattern;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\validator.dsAValidator::validateElement()
	 */
	public function validateElement() {
		$value = $this->element->getValue();

		$result = TRUE;
		if (dsStringTools::isFilled($value)) {
			$this->log->debug("Pattern for validation: " . $this->pattern);
			if (!preg_match("#" . $this->pattern . "#is", $value)) {
				$result = FALSE;
			}
		}

		return $result;
	}
}