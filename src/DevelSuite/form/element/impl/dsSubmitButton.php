<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2011 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\core\form\element\impl;

use DevelSuite\core\form\element\dsButtonValueConstants;
use DevelSuite\core\form\element\dsButtonNameConstants;
use DevelSuite\core\form\element\dsAButtonElement;

/**
 * A submit button.
 *
 * @package DevelSuite\core\form\element\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsSubmitButton extends dsAButtonElement {
	/**
	 * Class constructor
	 *
	 * @param string $value
	 * 			Value of this button [optional]
	 */
	public function __construct($value = NULL) {
		parent::__construct(dsButtonNameConstants::SUBMIT, dsButtonValueConstants::SEND, $value);
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\core\form\element.dsAButtonElement::getHTML()
	 */
	public function getHTML() {
		// generate HTML
		$html = "<input";

		// set CSS class
		if (!empty($this->cssClass)) {
			$html .= " class='" . implode(" ", $this->cssClass) . "'";
		}

		$html .= " type='submit' id='dsFormSubmit' value='" . $this->value . "' name='" . $this->name . "' />\n";
		return $html;
	}
}