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
 * Represents a text area element.
 *
 * @package DevelSuite\form\element\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsTextArea extends dsAElement {
	private $content;
	private $cols;
	private $rows;

	/**
	 * Class constructor
	 *
	 * @param string $caption
	 * 			Caption of the element
	 * @param string $name
	 * 			Name of the element
	 * @param bool $mandatory
	 * 			TRUE if element should be mandatory [optional]
	 * @param bool $readOnly
	 * 			TRUE if element should be readonly [optional]
	 */
	public function __construct($caption, $name, $rows, $cols) {
		parent::__construct($caption, $name);

		$this->rows = $rows;
		$this->cols = $cols;
	}

	/**
	 * Set content of the textarea
	 *
	 * @param string $content
	 * 			Content of the textarea
	 */
	public function setContent($content) {
		$this->content = $content;
	}

	/**
	 * Set value of the textarea
	 *
	 * @param string $value
	 * 			Value of the textarea
	 */
	public function setValue($value) {
		$this->content = $value;
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAElement::refillValues()
	 */
	public function refillValues() {
		$this->content = $this->getValue();
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAElement::getHTML()
	 */
	public function getHTML() {
		// generate HTML
		$html = "<textarea";

		// set CSS class
		if (!empty($this->cssClass)) {
			$html .= " class='" . implode(" ", $this->cssClass) . "'";
		}
		$html .= " id='" . $this->name . "' name='" . $this->name . "'";

		// set rows
		if (isset($this->rows)) {
			$html .= " rows='" . $this->rows . "'";
		}

		// set cols
		if (isset($this->cols)) {
			$html .= " cols='" . $this->cols. "'";
		}

		// set readonly
		if ($this->readOnly) {
			$html .= " readonly='readonly'";
		}

		// set disabled
		if ($this->disabled) {
			$html .= "disabled='disabled' ";
		}
		$html .= ">";

		// set content
		if (isset($this->content)) {
			$html .= $this->content;
		}
		$html .= "</textarea>\n";

		$code = "<div class='dsform-type-text ";
		// set error message
		if (!$this->isValid()) {
			$code .= "error'>\n";
			$code .= "<strong class='dsform-message'>" . $this->getErrorMessage() . "</strong>\n";
		} else {
			$code .= "'>\n";
		}

		$code .= $this->addLabel($html);
		$code .= "</div>\n";

		return $code;
	}
}