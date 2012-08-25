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
use DevelSuite\form\element\dsAElement;

/**
 * Represents a group of checkbox elements.
 *
 * @package DevelSuite\form\element\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsCheckboxGroup extends dsCompositeElement {
	/**
	 * Retrieve the name attribute of this element
	 *
	 * @return Name of this element
	 */
	public function getName() {
		return $this->name;
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\form\element.dsCompositeElement::addChild()
	 */
	public function addChild(dsAElement $child) {
		if ($child instanceof dsCheckbox) {
			$child->setGroup($this);
			// set readonly if group is set to readonly
			if ($this->readOnly) {
				$child->setReadOnly($this->readOnly);
			}
			
			if ($this->disabled) {
				$child->setDisabled();
			}

			parent::addChild($child);
		}
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAElement::getHTML()
	 */
	public function getHTML() {
		// generate HTML
		$html = "<div class='dsform-type-chkgrp";
		
		// set CSS class
		if (!empty($this->cssClass)) {
			$html .= " " . implode(" ", $this->cssClass);
		}
		
		$html .= "' id='" . $this->name . "'>\n";
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