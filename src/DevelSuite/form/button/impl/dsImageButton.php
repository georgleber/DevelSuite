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
 * Represents a image button.
 *
 * @package DevelSuite\form\button\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsImageButton extends dsAButton {
	/**
	 * Path to the image for this button
	 * @var string
	 */
	private $imageSrc;

	/**
	 * Constructor
	 *
	 * @param string $value
	 * 			Value of this button [optional]
	 */
	public function __construct($name, $value, $imageSrc) {
		parent::__construct($name, NULL, $value);

		$this->imageSrc = $imageSrc;
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

		$html .= " type='image' src='" . $this->imageSrc . "' value='" . $this->value . "' name='" . $this->name . "' />\n";
		return $html;
	}
}