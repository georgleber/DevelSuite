<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form\element\impl;

use DevelSuite\form\element\validator\impl\dsCaptchaValidator;

use DevelSuite\form\element\dsAElement;

/**
 * Represents a captcha element.
 *
 * @package DevelSuite\form\element\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsCaptcha extends dsAElement {
	private $exercise;

	public function __construct($caption) {
		parent::__construct($caption, "captcha");
		$this->addCssClass("captcha");

		$this->addValidator(new dsCaptchaValidator($this));
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAElement::refillValues()
	 */
	public function refillValues() {
		// do nothing
	}

	public function setExercise($exercise) {
		$this->exercise = $exercise;
	}
	/* (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAElement::addLabel()
	 */
	protected function addLabel($html) {
		// generate label HTML
		$label = "<label for='" . $this->name . "'>" . $this->caption . " " . $this->exercise . "?";

		// set mandatory
		if($this->mandatory) {
			$label .= "<em>*</em>";
		}
		$label .= "</label>\n";

		// append / prepend label
		if($this->appendLabel) {
			$html .= $label;
		} else {
			$html = $label . $html;
		}

		return $html;
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAElement::getHTML()
	 */
	public function getHTML() {
		// generate HTML
		$html = "<input type='text'";

		// set CSS class
		if (!empty($this->cssClass)) {
			$html .= " class='" . implode(" ", $this->cssClass) . "'";
		}
		$html .= " id='" . $this->name . "' name='" . $this->name . "' />\n";

		$code = "<div class='dsform-type-captcha";
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