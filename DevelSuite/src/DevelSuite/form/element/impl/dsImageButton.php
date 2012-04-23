<?php
namespace DevelSuite\core\form\element\impl;

use DevelSuite\core\form\element\dsAButtonElement;
use DevelSuite\core\form\element\dsButtonValueConstants;
use DevelSuite\core\form\element\dsButtonNameConstants;

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
	 * @see DevelSuite\core\form\element.dsAButtonElement::getHTML()
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