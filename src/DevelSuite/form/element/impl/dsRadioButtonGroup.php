<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form\element\impl;

use DevelSuite\form\element\dsAElement;
use DevelSuite\form\element\dsCompositeElement;

/**
 * Represents a group of radio button elements.
 *
 * @package DevelSuite\form\element\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsRadioButtonGroup extends dsCompositeElement {
	private $checkCount = 0;

	/* (non-PHPdoc)
	 * @see DevelSuite\form\element.dsCompositeElement::addChild()
	 */
	public function addChild(dsAElement $child) {
		if ($child instanceof dsRadioButton) {
			if ($child->getChecked() && $this->checkCount < 1) {
				$this->checkCount++;
			} else {
				# FIXME: add logging!
				$child->setChecked(FALSE);
			}
			$child->setGroup($this);

			// set readonly if group is set to readonly
			if ($this->readOnly) {
				$child->setReadOnly($this->readOnly);
			}

			parent::addChild($child);
		}
	}

	/**
	 * Retrieve the name attribute of this element
	 *
	 * @return Name of this element
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Selects the RadioButton with the the given value
	 *
	 * @param string $value
	 * 			Valeu of the selected RadioButton
	 */
	public function setValue($value) {
		foreach ($this->childElements as $child) {
			if ($child->getElementValue()) {
				$child->setChecked();
			}
		}
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAElement::getHTML()
	 */
	public function getHTML() {
		// generate HTML
		$html = "<div class='dsform-type-radiogrp' id='" . $this->name . "'>\n";

		$html .= "<p>" . $this->caption;
		// set mandatory
		if ($this->mandatory) {
			$html .= "<em>*</em>";
		}
		$html .= "</p>\n";

		// add html of childElements
		foreach ($this->childElements as $child) {
			$html .= $child->getHTML();
		}

		$html .= "</div>\n";
		return $html;
	}
}