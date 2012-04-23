<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2011 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\core\form\element\impl;

use DevelSuite\core\form\element\dsAElement;

/**
 * Represents a element for dynamic content.
 *
 * @package DevelSuite\core\form\element\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsDynamicContent extends dsAElement {
	private $content;

	/**
	 * Class constructor
	 *
	 * @param string $content
	 * 			Content to add to the form
	 */
	public function __construct($content) {
		$this->content = $content;
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\core\form\element.dsAElement::refillValues()
	 */
	public function refillValues() {
		// do nothing
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\core\form\element.dsAElement::getHTML()
	 */
	public function getHTML() {
		return $this->content;
	}
}