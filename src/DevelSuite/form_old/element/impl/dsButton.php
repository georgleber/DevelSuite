<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form_old\element\impl;

use DevelSuite\form\element\dsAElement;

/**
 * A button
 *
 * @package DevelSuite\form\element\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsButton extends dsAElement {
	/**
	 * Class constructor
	 *
	 * @param string $value
	 * 			Value of the button
	 */
	public function __construct($caption, $name) {
		parent::__construct($caption, $name);
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAElement::refillValues()
	 */
	public function refillValues() {
		// do nothing
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAButtonElement::getHTML()
	 */
	public function getHTML() {
		// generate HTML
		$html = "<div class='dsform-type-button'>";
		$html .= "<input";

		// set CSS class
		if (!empty($this->cssClass)) {
			$html .= " class='" . implode(" ", $this->cssClass) . "'";
		}

		$html .= " type='button' id='" . $this->name . "' value='" . $this->caption . "' name='" . $this->name . "' />\n";
		$html .= "</div>\n";

		return $html;
	}
}