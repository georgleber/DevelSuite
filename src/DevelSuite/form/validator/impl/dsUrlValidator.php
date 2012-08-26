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
use DevelSuite\util\dsStringTools;

/**
 * Validator for URL inputs.
 *
 * @package DevelSuite\form\validator\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsUrlValidator extends dsAValidator {
	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\validator.dsAValidator::validateElement()
	 */
	public function validateElement() {
		$result = TRUE;
		$url = $this->element->getValue();

		$this->log->debug("URL validation: Checking value: " . $url);
		if (dsStringTools::isFilled($url)) {
			$result = preg_match("!^(http|https)+(://)+(www\.)?([a-z0-9\.-]{3,})\.[a-z]{2,4}(/?.*)?$!i", $url);
		}

		return $result;
	}
}