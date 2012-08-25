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
 * Represents a hidden input element.
 *
 * @package DevelSuite\form\element\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsHiddenInput extends dsASimpleElement {
	/**
	 * Value of this hidden element
	 * @var string
	 */
	private $value;

	/**
	 * Class constructor
	 *
	 * @param string $name
	 * 			Name of the hidden element
	 * @param string $value
	 * 			Value of the hidden element
	 */
	public function __construct($name, $value) {
		parent::__construct(NULL, $name);

		$this->value = $value;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAElement::validate()
	 */
	public function validate() {
		return TRUE;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAElement::populate()
	 */
	protected function populate() {
		// do nothing
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\element.dsASimpleElement::getHTML()
	 */
	protected function getHTML() {
		// create HTML
		$html = "<input type='hidden' name='" . $this->name . "' value='" . $this->value . "' />\n";
		return $html;
	}
}