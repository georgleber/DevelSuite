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
 * A cancel button
 *
 * @package DevelSuite\core\form\element\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsCancelButton extends dsAButtonElement {
	/**
	 * Class constructor
	 *
	 * @param string $value
	 * 			Value of the button
	 */
	public function __construct($value = NULL) {
		parent::__construct(dsButtonNameConstants::CANCEL, dsButtonValueConstants::CANCEL, $value);
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\core\form\element.dsAButtonElement::getHTML()
	 */
	public function getHTML() {
		// generate HTML
		$html = "<input";

		// set CSS class
		if (isset($this->cssClass)) {
			$html .= " class='" . $this->cssClass . "'";
		}

		$html .= " type='button' id='dsFormCancel' value='" . $this->value . "' name='" . $this->name . "' />\n";
		return $html;
	}
}