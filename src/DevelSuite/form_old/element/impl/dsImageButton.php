<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form_old\element\impl;

use DevelSuite\form\element\dsAButtonElement;
use DevelSuite\form\element\dsButtonValueConstants;
use DevelSuite\form\element\dsButtonNameConstants;

/**
 * FIXME
 *
 * @package DevelSuite\form\element
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsImageButton extends dsAButtonElement {
	private $imageSrc;
	
	/**
	* Class constructor
	*
	* @param string $value
	* 			Value of the button
	*/
	public function __construct($name, $value, $imageSrc) {
		parent::__construct($name, NULL, $value);
		
		$this->imageSrc = $imageSrc;
	}
	
	/* (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAButtonElement::getHTML()
	*/
	public function getHTML() {
		// generate HTML
		$html = "<input";
	
		// set CSS class
		if (isset($this->cssClass)) {
			$html .= " class='" . $this->cssClass . "'";
		}
	
		$html .= " type='image' src='" . $this->imageSrc . "' id='" . $this->name . "' value='" . $this->value . "' name='" . $this->name . "' />\n";
		return $html;
	}
}