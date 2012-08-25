<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form_old\element\impl;

use DevelSuite\form\element\dsButtonValueConstants;
use DevelSuite\form\element\dsButtonNameConstants;
use DevelSuite\form\element\dsAButtonElement;

/**
 * A click button
 *
 * @package DevelSuite\form\element\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsClickButton extends dsAButtonElement {
	/**
	 * Class constructor
	 *
	 * @param string $name
	 * 		 	name of the button
	 * @param string $value
	 * 			value of the button
	 */
	public function __construct($name, $value) {
		parent::__construct($name, $value);
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAElement::getHTML()
	 */
	public function getHTML() {
		// generate HTML
		$html = "<input";

		// set CSS class
		if (isset($this->cssClass)) {
			$html .= " class='" . $this->cssClass . "'";
		}

		$html .= " type='button' id='" . $this->name . "' value='" . $this->value . "' name='" . $this->name . "' />\n";
		return $html;
	}
}