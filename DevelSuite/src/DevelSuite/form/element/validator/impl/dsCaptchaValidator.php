<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2011 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\core\form\element\validator\impl;

use DevelSuite\core\form\element\impl\dsCheckbox;

use DevelSuite\core\form\element\impl\dsRadioButton;

use DevelSuite\core\form\element\impl\dsCheckboxGroup;

use DevelSuite\core\form\element\impl\dsSelect;

use DevelSuite\core\form\element\impl\dsRadioButtonGroup;

use DevelSuite\core\form\element\dsCompositeElement;

use DevelSuite\core\util\dsStringTools;

use DevelSuite\core\i18n\dsResourceBundle;
use DevelSuite\core\form\element\validator\dsAValidator;

/**
 * Validator for email elements.
 *
 * @package DevelSuite\core\form\element\validator\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsCaptchaValidator extends dsAValidator {
	/* (non-PHPdoc)
	 * @see DevelSuite\core\form\element\validator.dsAValidator::init()
	 */
	protected function init() {
		$iniArr = dsResourceBundle::getBundle(dirname(__FILE__) . DS . "validation");

		$errorMessage = sprintf($iniArr['dsCaptchaValidator.error'], $this->element->getCaption());
		$this->errorMessage = $errorMessage;
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\core\form\element\validator.dsAValidator::validate()
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