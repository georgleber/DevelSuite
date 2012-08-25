<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form\validator\impl;

use DevelSuite\i18n\dsResourceBundle;

use DevelSuite\form\validator\dsAValidator;
use DevelSuite\util\dsStringTools;

/**
 * Validator for email elements.
 *
 * @package DevelSuite\form\validator\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsEmailValidator extends dsAValidator {
	/* 
	 * (non-PHPdoc)
	 * @see DevelSuite\form\validator.dsAValidator::init()
	 */
	protected function init() {
		$iniArr = dsResourceBundle::getBundle(dirname(__FILE__), "validation");
		$this->errorMessage = $iniArr['dsEmailValidator.error'];
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\validator.dsAValidator::validateElement()
	 */
	public function validateElement() {
		$result = TRUE;
		$value = $this->element->getValue();

		if (dsStringTools::isFilled($value)) {
			$result = preg_match('/^[a-zA-Z0-9!#\$%&\'*+\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#\$%&\'*+\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/', $value);
		}

		return $result;
	}
}