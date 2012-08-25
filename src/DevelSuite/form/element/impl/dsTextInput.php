<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form\element\impl;

use DevelSuite\form\element\dsASimpleElement;
use DevelSuite\util\dsStringTools;

/**
 * Represents a text input element.
 *
 * @package DevelSuite\form\element\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsTextInput extends dsASimpleElement {
	/**
	 * Value of this element
	 * @var string
	 */
	private $value;
	
	/**
	 * Set this element readOnly
	 * @var bool
	 */
	private $readOnly;
	
	/**
	 * Enable / disable autocomplete for this element
	 * @var bool
	 */
	private $autoComplete;

	/**
	 * Set the value of this element
	 *
	 * @param string $value
	 * 			Value of this element
	 */
	public function setValue($value) {
		$this->value = $value;
		return $this;
	}

	/**
	 * Set this element readOnly
	 *
	 * @param bool $readOnly
	 * 			TRUE, if this element should be readOnly
	 */
	public function setReadOnly($readOnly = TRUE) {
		$this->readOnly = $readOnly;
		return $this;
	}

	/**
	 * Disables the textfield autocomplete functionality
	 */
	public function disableAutoComplete() {
		$this->autoComplete = FALSE;
		return $this;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAElement::populate()
	 */
	protected function populate() {
		$this->value = $this->getValue();
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\element.dsASimpleElement::getHTML()
	 */
	protected function getHTML() {
		// create HTML
		$html = "<input type='text'";

		// set CSS class
		if (!empty($this->cssClass)) {
			$html .= " class='" . implode(" ", $this->cssClass) . "'";
		}

		$html .= " name='" . $this->name . "'";

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
		return $html;
	}
}