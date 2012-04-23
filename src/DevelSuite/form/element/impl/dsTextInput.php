<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form\element\impl;

use DevelSuite\util\dsStringTools;

use DevelSuite\form\element\dsAElement;

/**
 * Represents a text input element.
 *
 * @package DevelSuite\form\element\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsTextInput extends dsAElement {
	private $value;
	private $autoComplete = TRUE;

	/**
	 * Set the value of this element
	 *
	 * @param string $value
	 * 			Value of this element
	 */
	public function setValue($value) {
		$this->value = $value;
	}

	/**
	 * Disables the textfield autocomplete functionality
	 */
	public function disableAutoComplete() {
		$this->autoComplete = FALSE;
		return $this;
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAElement::refillValues()
	 */
	public function refillValues() {
		$this->value = $this->getValue();
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAElement::getHTML()
	 */
	public function getHTML() {
		// generate HTML
		$html = "<input type='text'";

		// set CSS class
		if (!empty($this->cssClass)) {
			$html .= " class='" . implode(" ", $this->cssClass) . "'";
		}
		$html .= " id='" . $this->name . "' name='" . $this->name . "' ";

		// set value
		if (dsStringTools::isFilled($this->value)) {
			$html .= "value='" . $this->value . "' ";
		}

		// set readonly
		if ($this->readOnly) {
			$html .= "readonly='readonly' ";
		}

		// set disabled
		if ($this->disabled) {
			$html .= "disabled='disabled' ";
		}

		// set autocomplete to off
		if ($this->autoComplete == FALSE) {
			$html .= "autocomplete='off' ";
		}
		$html .= "/>\n";

		$code = "<div class='dsform-type-text";
		// set error message
		if (!$this->isValid()) {
			$code .= " error'>\n";
			$code .= "<strong class='dsform-message'>" . $this->getErrorMessage() . "</strong>\n";
		} else {
			$code .= "'>\n";
		}

		$code .= $this->addLabel($html);
		$code .= "</div>\n";

		return $code;
	}
}