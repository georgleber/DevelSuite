<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2011 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\core\form\element\impl;

use DevelSuite\core\form\element\dsCompositeElement;

/**
 * Represents an option group of a select element.
 *
 * @package DevelSuite\core\form\element
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsOptGroup extends dsCompositeElement {
	private $label;

	/**
	 * Class constructor
	 *
	 * @param string $label
	 * 			Label of the group
	 */
	public function __construct($label) {
		$this->label = $label;
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\core\form\element.dsAElement::getHTML()
	 */
	public function getHTML() {
		// generate HTML
		$html = "<optgroup label='" . $this->label . "'>";

		// add html of childElements
		foreach ($this->childElements as $child) {
			$html .= $child->getHTML();
		}

		$html .= "</optgroup>\n";
		return $html;
	}
}