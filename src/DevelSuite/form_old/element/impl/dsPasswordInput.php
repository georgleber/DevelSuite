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
 * Represents a password input element.
 *
 * @package DevelSuite\form\element\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsPasswordInput extends dsAElement {
	/* (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAElement::refillValues()
	 */
	public function refillValues() {
		// DO NOTHING!
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAElement::getHTML()
	 */
	public function getHTML() {
		// generate HTML
		$html = "<input type='password'";

		// set CSS class
		if (!empty($this->cssClass)) {
			$html .= " class='" . implode(" ", $this->cssClass) . "'";
		}
		$html .= " id='" . $this->name . "' name='" . $this->name . "' />\n";

		$code = "<div class='dsform-type-text";
		// set error message
		if (!$this->isValid()) {
			$code .= " error'>\n";
			$code .= "<strong class='dsform-message'>" . $this->getErrorMessage() . "</strong>\n";
		} else {
			$code .= "'>\n";
		}

		$code .= $this->addLabel($html);
		$code .= "</div>\n";

		return $code;
	}
}