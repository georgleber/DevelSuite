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

/**
 * Represents a dynamic content element for adding addtional HTML code
 *
 * @package DevelSuite\form\element\impl;
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsDynamicContent extends dsASimpleElement {
	/**
	 * HTML content of this element
	 * @var string
	 */
	private $content;

	/**
	 * Constructor
	 *
	 * @param string $content
	 * 			Content to add to the form
	 */
	public function __construct($content) {
		$this->content = $content;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAElement::validate()
	 */
	public function validate() {
		return TRUE;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAElement::populate()
	 */
	public function populate() {
		// do nothing
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\element.dsASimpleElement::addLabel()
	 */
	protected function addLabel() {
		return "";
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\element.dsASimpleElement::addErrorSpan()
	 */
	protected function addErrorSpan() {
		return "";
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\element.dsASimpleElement::getHTML()
	 */
	protected function getHTML() {
		return $this->content;
	}
}