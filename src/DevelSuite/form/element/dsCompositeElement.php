<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form\element;

/**
 * Elements can be composite in this CompositeElement.
 *
 * @package DevelSuite\form\element
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsCompositeElement extends dsAElement {
	protected $childElements = array();

	/**
	 * @return The child elements
	 */
	public function getChildElements() {
		return $this->childElements;
	}

	/**
	 * Add a child element
	 *
	 * @param dsAElement $child
	 * 			The child to add
	 */
	public function addChild(dsAElement $child) {
		$this->childElements[] = $child;
	}

	/**
	 * Find a element in the list of children and remove it
	 *
	 * @param dsAElement $child
	 * 			The child to remove
	 * @return bool
	 * 			TRUE if the child could be removed
	 */
	public function removeChild(dsAElement $child) {
		$key = array_search($child, $this->childElements);
		if($key === FALSE) {
			return FALSE;
		}

		unset($this->childElements[$key]);
		return TRUE;
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAElement::refillValues()
	 */
	public function refillValues() {
		foreach ($this->childElements as $child) {
			$child->refillValues();
		}
	}
}