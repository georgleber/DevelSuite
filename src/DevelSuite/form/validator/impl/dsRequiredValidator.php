<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form\validator\impl;

use DevelSuite\form\element\impl\dsCheckbox;

use DevelSuite\form\element\impl\dsCheckboxGroup;

use DevelSuite\form\element\impl\dsRadioButton;

use DevelSuite\form\element\impl\dsRadioButtonGroup;

use DevelSuite\form\element\impl\dsSelect;

use DevelSuite\i18n\dsResourceBundle;

use DevelSuite\form\element\impl\dsFileInput;
use DevelSuite\form\validator\dsAValidator;
use DevelSuite\util\dsStringTools;

/**
 * Validator for all mandatory elements.
 *
 * @package DevelSuite\form\validator\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsRequiredValidator extends dsAValidator {
	/* 
	 * (non-PHPdoc)
	 * @see DevelSuite\form\validator.dsAValidator::init()
	 */
	protected function init() {
		$iniArr = dsResourceBundle::getBundle(dirname(__FILE__), "validation");

		$errorMessage = "";
		if ($this->element instanceof dsSelect
		|| $this->element instanceof dsRadioButtonGroup
		|| $this->element instanceof dsRadioButton
		|| $this->element instanceof dsCheckboxGroup
		|| $this->element instanceof dsCheckbox) {
			$errorMessage = sprintf($iniArr['dsRequiredValidator.selecterror'], $this->element->getCaption());
		} else {
			$errorMessage = sprintf($iniArr['dsRequiredValidator.texterror'], $this->element->getCaption());
		}

		$this->errorMessage = $errorMessage;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\validator.dsAValidator::validateElement()
	 */
	public function validateElement() {
		if ($this->element instanceof dsFileInput) {
			return TRUE;
		}

		$result = TRUE;
		$value = $this->element->getValue();

		if(is_string($value)) {
			if (dsStringTools::isNullOrEmpty($value)) {
				$result = FALSE;
			}
		} else {
			if (empty($value)) {
				$result = FALSE;
			}
		}

		return $result;
	}
}