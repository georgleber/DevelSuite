<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2011 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\core\form\element\impl;

use DevelSuite\core\form\element\dsAElement;

/**
 * Represents a hidden form element.
 *
 * @package DevelSuite\core\form\element\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsHiddenInput extends dsAElement {
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

		if (!isset($this->value)) {
			# FIXME
			# throw exception
		}
		$this->value = $value;
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\core\form\element.dsAElement::refillValues()
	 */
	public function refillValues() {
		// do nothing
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\core\form\element.dsAElement::getHTML()
	 */
	public function getHTML() {
		// generate HTML
		return "<input type='hidden' name='" . $this->name . "' value='" . $this->value . "' />\n";
	}
}