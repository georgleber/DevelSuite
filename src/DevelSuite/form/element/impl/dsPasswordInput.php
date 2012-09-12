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
 * Represents a password input element.
 *
 * @package DevelSuite\form\element\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsPasswordInput extends dsASimpleElement {
	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAElement::populate()
	 */
	public function populate() {
		// do nothing
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\element.dsASimpleElement::getHTML()
	 */
	protected function getHTML() {
		$this->addCssClass("text");
		
		// generate HTML
		$html = "<input type='password'";

		// set CSS class
		if (!empty($this->cssClasses)) {
			$html .= " class='" . implode(" ", $this->cssClasses) . "'";
		}

		$html .= " name='" . $this->name . "' />\n";
		return $html;
	}
}