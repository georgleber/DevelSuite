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

/**
 * Represents a option element.
 *
 * @package DevelSuite\form\element\impl;
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsOption extends dsASimpleElement {
	/**
	 * Value of this option element
	 * @var string
	 */
	private $value;

	/**
	 * State of selection for this element
	 * @var bool
	 */
	private $selected = FALSE;

	/**
	 * Constructor
	 *
	 * @param string $caption
	 * 		Caption of this option
	 * @param string $value
	 * 		Value of this option
	 */
	public function __construct($caption, $value) {
		parent::__construct($caption, NULL);

		$this->value = $value;
	}

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

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAElement::populate()
	 */
	protected function populate() {
		$value = $this->getValue();
		if (isset($value)) {
			$this->setSelected();
		}
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\element.dsASimpleElement::getHTML()
	 */
	protected function getHTML() {
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

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\element.dsASimpleElement::addLabel()
	 */
	protected function addLabel() {
		return "";
	}
}