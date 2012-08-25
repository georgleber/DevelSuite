<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form\element\impl;

use DevelSuite\form\element\dsASimpleElement;

use DevelSuite\form\element\dsAElement;

/**
 * Represents a text area element.
 *
 * @package DevelSuite\form\element\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsTextArea extends dsASimpleElement {
	/**
	 * Content of the text area
	 * @var string
	 */
	private $content;

	/**
	 * Set this element readOnly
	 * @var bool
	 */
	private $readOnly;

	/**
	 * Row count for the text area
	 * @var int
	 */
	private $rows;

	/**
	 * Column count for the text area
	 * @var int
	 */
	private $cols;

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
	public function __construct($caption, $name, $rows = 15, $cols = 50) {
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
		return $this;
	}

	/**
	 * Set value (content) of the textarea
	 *
	 * @param string $value
	 * 			Value of the textarea
	 */
	public function setValue($value) {
		$this->content = $value;
		return $this;
	}

	/**
	 * Set this element readOnly
	 *
	 * @param bool $readOnly
	 * 			TRUE, if this element should be readOnly
	 */
	public function setReadOnly($readOnly = TRUE) {
		$this->readOnly = $readOnly;
		return $this;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAElement::populate()
	 */
	protected function populate() {
		$this->content = $this->getValue();
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\element.dsASimpleElement::getHTML()
	 */
	protected function getHTML() {
		// generate HTML
		$html = "<textarea";

		// set CSS class
		if (!empty($this->cssClasses)) {
			$html .= " class='" . implode(" ", $this->cssClasses) . "'";
		}
		$html .= " name='" . $this->name . "'";

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
		return $html;
	}
}