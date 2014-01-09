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
use DevelSuite\util\dsStringTools;

use DevelSuite\form\validator\dsAValidator;

abstract class dsASimpleElement extends dsAElement {
	/**
	 * Append the caption of the elements
	 * @var bool
	 */
	protected $appendLabel = FALSE;
    
    /**
	 * Additional css class for labels
	 * @var array
	 */
    private $labelCss = array();

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
	 * Append the label to this element
	 *
	 * @param bool $appendLabel
	 * 		TRUE, if label should be appended
	 */
	public function appendLabel($appendLabel = TRUE) {
		$this->appendLabel = $appendLabel;
		return $this;
	}
    
    /**
	 * Set a CSS class for this elements label.
	 *
	 * @param string $class
	 * 			CSS class name for this elements label
	 */
	public function addLabelCss($class) {
		$this->labelCss[] = $class;
		return $this;
	}

	/**
	 * Add a label element as caption
	 */
	protected function addLabel() {
		$label = "<label for='" . $this->name . "'";
        
        // set CSS class
		if (!empty($this->labelCss)) {
			$label .= " class='" . implode(" ", $this->labelCss) . "'";
		}
        
        $label .= ">" . $this->caption;

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
	protected function addErrorSpan() {
		$html = "<span class='dsform-errorMsg'>";

		if (dsStringTools::isFilled($this->errorMessage)) {
			$html .= $this->errorMessage;
		}

		$html .= "</span>";
		return $html;
	}
}