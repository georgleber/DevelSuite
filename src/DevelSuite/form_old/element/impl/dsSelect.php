<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form_old\element\impl;

use DevelSuite\form\element\dsCompositeElement;

/**
 * Represents a select element.
 *
 * @package DevelSuite\form\element\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsSelect extends dsCompositeElement {
	private $multiple;
	private $size = 1;

	/**
	 * Set number of visible entries
	 *
	 * @param int $size
	 * 			Number of visible entries (must be > 0)
	 */
	public function setSize($size) {
		if ($size > 0) {
			$this->size = $size;
		}

		return $this;
	}

	/**
	 * Set selection of element to multiple
	 *
	 * @param bool $multiple
	 * 			TRUE, if list should be multi selectable
	 */
	public function setMultiple($multiple = TRUE) {
		$this->multiple = $multiple;
		return $this;
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\form\element.dsCompositeElement::getHTML()
	 */
	public function getHTML() {
		// generate HTML
		$html = "<select";

		// set CSS class
		if (!empty($this->cssClass)) {
			$html .= " class='" . implode(" ", $this->cssClass) . "'";
		}

		if ($this->size == 1) {
			$html .= " id='" . $this->name ."' name='" . $this->name . "' size='" . $this->size . "' ";
		} else {
			$html .= " id='" . $this->name ."' name='" . $this->name . "[]' size='" . $this->size . "' ";
		}

		// set disabled
		if ($this->disabled) {
			$html .= "disabled='disabled' ";
		}

		// set multiple
		if ($this->multiple) {
			$html .= "multiple='multiple'";
		}
		$html .= ">\n";

		// add html of childElements
		foreach ($this->childElements as $child) {
			$html .= $child->getHTML();
		}
		$html .= "</select>\n";

		$code = "<div class='dsform-type-select";
		// set error message
		if (!$this->isValid()) {
			$code .= "error'>\n";
			$code .= "<strong class='dsform-message'>" . $this->getErrorMessage() . "</strong>\n";
		} else {
			$code .= "'>\n";
		}

		$code .= $this->addLabel($html);
		$code .= "</div>\n";

		return $code;
	}
}