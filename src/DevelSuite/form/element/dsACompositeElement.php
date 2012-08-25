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
abstract class dsACompositeElement extends dsAElement {
	/**
	 * Contains all child elements
	 * @var array
	 */
	protected $childElements = array();

	/**
	 * Contains all allowed elements for this composite element
	 * @var array
	 */
	protected $allowedElements = array();

	/**
	 * Returns the child elements
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
		$class = get_class($child);

		if (empty($this->allowedElements) || array_key_exists($class, $this->allowedElements)) {
			$this->childElements[] = $child;
		}

		// FIXME:
		// throw exception
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

	/* 
	 * (non-PHPdoc)
	 * @see DevelSuite\form\element.dsAElement::populate()
	 */
	protected function populate() {
		foreach ($this->childElements as $child) {
			$child->populate();
		}
	}
}