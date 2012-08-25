<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form\element;

/**
 * Superclass for all simple form elements
 *
 * @package DevelSuite\form\element
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
use DevelSuite\form\validator\dsAValidator;

abstract class dsASimpleElement extends dsAElement {
	/**
	 * Append the caption of the elements
	 * @var bool
	 */
	protected $appendLabel = FALSE;

	/**
	 * Get the HTML code of the specific form element
	 */
	abstract protected function getHTML();

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAElement::buildHTML()
	 */
	public function buildHTML() {
		$html = "";
		if ($this->appendLabel) {
			$html .= $this->getHTML();
			$html .= $this->addLabel();
		} else {
			$html .= $this->addLabel();
			$html .= $this->getHTML();
		}

		$html .= $this->addErrorSpan();
		return $html;
	}

	/**
	 * Add a label element as caption
	 */
	protected function addLabel() {
		$label = "<label for='" . $this->name . "'>" . $this->caption;

		// set mandatory
		if($this->mandatory) {
			$label .= "<em>*</em>";
		}

		$label .= "</label>\n";
		return $label;
	}

	/**
	 * Add a span element for error messages
	 */
	private function addErrorSpan() {
		$html = "<span class='dsform-errorMsg'>";

		if (!$this->isValid()) {
			$html .= $this->errorMessage;
		}

		$html .= "</span>";
		return $html;
	}
}