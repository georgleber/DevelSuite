<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form\button\impl;

use DevelSuite\form\button\dsAButton;
use DevelSuite\form\constants\dsButtonNameConstants;
use DevelSuite\form\constants\dsButtonValueConstants;

/**
 * Represents a cancel button.
 *
 * @package DevelSuite\form\button\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsCancelButton extends dsAButton {
	/**
	 * Constructor
	 *
	 * @param string $value
	 * 			Value of this button [optional]
	 */
	public function __construct($value = NULL) {
		parent::__construct(dsButtonNameConstants::CANCEL, dsButtonValueConstants::CANCEL, $value);
	}

	/* 
	 * (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAButton::getHTML()
	 */
	public function getHTML() {
		// generate HTML
		$html = "<input";

		// set CSS class
		if (!empty($this->cssClass)) {
			$html .= " class='" . implode(" ", $this->cssClass) . "'";
		}

		$html .= " type='button' value='" . $this->value . "' name='" . $this->name . "' />\n";
		return $html;
	}
}