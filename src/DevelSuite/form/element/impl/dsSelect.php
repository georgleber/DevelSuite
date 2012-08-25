<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form\element\impl;

use DevelSuite\form\element\dsACompositeElement;

/**
 * Represents a select element.
 *
 * @package DevelSuite\form\element\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsSelect extends dsACompositeElement {
	/**
	 * Allow multiple selection
	 * @var bool
	 */
	private $multiple;

	/**
	 * Size of the list
	 * @var int
	 */
	private $size = 1;

	/**
	 * Constructor
	 *
	 * @param string $caption
	 * 		Caption for this element
	 * @param string $name
	 * 		Name of this element
	 */
	public function __construct($caption, $name) {
		parent::__construct($caption, $name);

		$this->allowedElements = array("dsOption", "dsOptGroup");
	}

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

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAElement::buildHTML()
	 */
	public function buildHTML() {
		$html = $this->addLabel();
		
		// generate HTML
		$html .= "<select";

		// set CSS class
		if (!empty($this->cssClasses)) {
			$html .= " class='" . implode(" ", $this->cssClasses) . "'";
		}

		if ($this->size == 1) {
			$html .= " name='" . $this->name . "' size='" . $this->size . "'";
		} else {
			$html .= " name='" . $this->name . "[]' size='" . $this->size . "'";
		}

		// set disabled
		if ($this->disabled) {
			$html .= " disabled='disabled'";
		}

		// set multiple
		if ($this->multiple) {
			$html .= " multiple='multiple'";
		}
		$html .= ">\n";

		// add html of childElements
		foreach ($this->childElements as $child) {
			$html .= $child->buildHTML();
		}

		$html .= "</select>\n";
		return $html;
	}

	/**
	 * Add a label to this composite element
	 */
	private function addLabel() {
		$label = "<label for='" . $this->name . "'>" . $this->caption;

		// set mandatory
		if($this->mandatory) {
			$label .= "<em>*</em>";
		}

		$label .= "</label>\n";
		return $label;
	}
}