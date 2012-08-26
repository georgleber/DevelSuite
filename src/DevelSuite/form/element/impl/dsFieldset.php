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
use DevelSuite\form\validator\impl\dsRequiredValidator;
use DevelSuite\form\element\dsACompositeElement;
use DevelSuite\util\dsStringTools;

/**
 * Represents a fieldset element.
 *
 * @package DevelSuite\form\element\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsFieldset extends dsACompositeElement {
	/**
	 * Contains a FileInput element
	 * @var bool
	 */
	private $containsFileInput = FALSE;

	/**
	 * Constructor
	 *
	 * @param string $caption
	 * 		Caption for this element
	 */
	public function __construct($caption) {
		parent::__construct($caption, NULL);
	}

	/**
	 * Returns if this fieldset contains a fiel input element
	 */
	public function containsFileInput() {
		return $this->containsFileInput;
	}

	/**
	 * Find an element by name
	 *
	 * @param string $elementName
	 * 		Name of the element
	 */
	public function findElement($elementName) {
		foreach ($this->childElements as $element) {
			if ($element instanceof dsFieldset) {
				$elem = $element->findElement($elementName);
				if ($elem != NULL) {
					return $elem;
				}
			} else {
				if ($element->getName() == $elementName) {
					return $element;
				}
			}
		}

		return NULL;
	}

	/**
	 * Checks if the form elements are valid.
	 */
	public function validate() {
		$validResult = TRUE;

		foreach ($this->childElements as $element) {
			if (!$element->validate()) {
				$validResult = FALSE;
			}
		}

		return $validResult;
	}

	/**
	 * Clears all values of the form
	 */
	public function clear() {
		foreach ($this->elements as $element) {
			if ($element instanceof dsFieldset) {
				$element->clear();
			} else {
				$element->setValue(NULL);
			}
		}
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\element.dsACompositeElement::addChild()
	 */
	public function addChild(dsAElement $child) {
		if ($child instanceof dsFileInput) {
			$this->containsFileInput = TRUE;
		}

		if ($child->isMandatory()) {
			$this->mandatory = TRUE;
			$child->addValidator(new dsRequiredValidator($child));
		}

		if ($this->disabled) {
			$child->setDisabled();
		}
			
		parent::addChild($child);
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAElement::buildHTML()
	 */
	public function buildHTML() {
		// generate HTML
		$html = "<fieldset";

		// set CSS class
		if (!empty($this->cssClasses)) {
			$html .= " class='" . implode(" ", $this->cssClasses) . "'";
		}
		$html .= ">\n";
		$html .= "<legend>" . $this->caption . "</legend>\n";
		$html .= "<ul>\n";

		// add html of childElements
		foreach ($this->childElements as $child) {
			$html .= "<li class='dsform-formRow'>\n";
			$html .= $child->buildHTML();
			$html .= "</li>\n";
		}

		$html .= "</ul>\n";
		$html .= "</fieldset>\n";
		return $html;
	}
}