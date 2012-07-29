<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form\element\validator\impl;

use DevelSuite\form\element\impl\dsCheckbox;

use DevelSuite\form\element\impl\dsRadioButton;

use DevelSuite\form\element\impl\dsCheckboxGroup;

use DevelSuite\form\element\impl\dsSelect;

use DevelSuite\form\element\impl\dsRadioButtonGroup;

use DevelSuite\form\element\dsCompositeElement;

use DevelSuite\util\dsStringTools;

use DevelSuite\i18n\dsResourceBundle;
use DevelSuite\form\element\validator\dsAValidator;

/**
 * Validator for email elements.
 *
 * @package DevelSuite\form\element\validator\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsCaptchaValidator extends dsAValidator {
	/* (non-PHPdoc)
	 * @see DevelSuite\form\element\validator.dsAValidator::init()
	 */
	protected function init() {
		$iniArr = dsResourceBundle::getBundle(dirname(__FILE__), "validation");

		$errorMessage = sprintf($iniArr['dsCaptchaValidator.error'], $this->element->getCaption());
		$this->errorMessage = $errorMessage;
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\form\element\validator.dsAValidator::validate()
	 */
	public function validateElement() {
		$result = TRUE;
		$value = $this->element->getValue();

		$captchaVal = NULL;
		if (!isset($_SESSION['captcha_val'])) {
			throw new \Exception("Es wurde kein Captcha-Wert gesetzt.");
		} else {
			$captchaVal = $_SESSION['captcha_val'];
		}

		if($captchaVal != md5($value)) {
			$result = FALSE;
		}

		return $result;
	}
}