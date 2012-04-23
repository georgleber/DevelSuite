<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2011 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\core\form\element\impl;

use DevelSuite\core\form\element\dsAElement;

/**
 * Represents an option of a select element.
 *
 * @package DevelSuite\core\form\element
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsOption extends dsAElement {
	private $selected;
	private $value;

	/**
	 * Set value of this option
	 *
	 * @param string $value
	 * 			Value of this option
	 */
	public function setValue($value) {
		$this->value = $value;
		return $this;
	}

	/**
	 * Set the option selected
	 *
	 * @param bool $selected
	 * 			TRUE, if option should be selected
	 */
	public function setSelected($selected = TRUE) {
		$this->selected = $selected;
		return $this;
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\core\form\element.dsAElement::refillValues()
	 */
	public function refillValues() {
		$value = $this->getValue();
		if (isset($value)) {
			$this->setSelected();
		}
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\core\form\element.dsAElement::getHTML()
	 */
	public function getHTML() {
		// generate HTML
		$html = "<option";

		// set CSS class
		if (!empty($this->cssClass)) {
			$html .= " class='" . implode(" ", $this->cssClass) . "'";
		}

		// set value
		if (isset($this->value)) {
			$html .= " value='" . $this->value . "'";
		}

		// set selected
		if ($this->selected) {
			$html .= " selected='selected'";
		}

		// set disabled
		if ($this->disabled) {
			$html .= " disabled='disabled'";
		}

		$html .= ">" . $this->caption . "</option>\n";
		return $html;
	}
}