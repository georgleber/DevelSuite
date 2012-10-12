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
 * Represents a radio button element.
 *
 * @package DevelSuite\form\element\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsRadioButton extends dsASimpleElement {
	/**
	 * Set this element readOnly
	 * @var bool
	 */
	private $readOnly;

	/**
	 * Value fo the radio button
	 * @var string
	 */
	private $value;

	/**
	 * Group of radio button
	 * @var dsRadioButtonGroup
	 */
	private $group;

	/**
	 * Is the radio button checked
	 * @var bool
	 */
	private $checked = FALSE;

	/**
	 * Class constructor
	 *
	 * @param string $caption
	 * 			Caption of the element
	 * @param string $name
	 * 			Name of the element
	 * @param string $value
	 * 			Value of the element
	 */
	public function __construct($caption, $name, $value = NULL) {
		parent::__construct($caption, $name);

		if (isset($value)) {
			$this->value = $value;
		} else {
			$this->value = $name;
		}
	}

	/**
	 * Set the radio button checked
	 *
	 * @param bool $checked
	 * 			TRUE if radio button should be checked (must be bool)
	 */
	public function setChecked($checked = TRUE) {
		$this->checked = $checked;
		return $this;
	}

	/**
	 * Returns if this radio button is checked
	 */
	public function isChecked() {
		return $this->checked;
	}

	/**
	 * Set a radio button group
	 *
	 * @param dsRadioButtonGroup group
	 * 			The group of the radio button
	 */
	public function setGroup($group) {
		$this->group = $group;
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

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAElement::populate()
	 */
	public function populate() {
		$value = $this->getValue();
		if (isset($value)) {
			$this->setChecked();
		}
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\element.dsASimpleElement::addLabel()
	 */
	protected function addLabel() {
		$label = "<label class='label-radiobutton' for='" . $this->name . "'>" . $this->caption;

		// set mandatory
		if($this->mandatory) {
			$label .= "<em>*</em>";
		}

		$label .= "</label>\n";
		return $label;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\element.dsASimpleElement::getHTML()
	 */
	protected function getHTML() {
		// generate HTML
		$html = "<input type='radio'";

		// set CSS class
		if (!empty($this->cssClasses)) {
			$html .= " class='" . implode(" ", $this->cssClasses) . "'";
		}

		// set name of group
		if (isset($this->group)) {
			$html .= " name='" . $this->group->getName() . "'";
		} else {
			$html .= " name='" . $this->name . "'";
		}

		// set value
		if (isset($this->value)) {
			$html .= " value='" . $this->value . "'";
		}

		// set readonly
		if ($this->readOnly) {
			$html .= "readonly='readonly' ";
		}

		// set checked
		if ($this->checked) {
			$html .= " checked='checked'";
		}

		$html .= "/>\n";
		return $html;
	}
}