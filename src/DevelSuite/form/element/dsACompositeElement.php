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
use Monolog\Handler\StreamHandler;

use Monolog\Logger;

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
		$class = $this->parseClassname(get_class($child));

		$log = new Logger("CompositeElement");
		$log->pushHandler(new StreamHandler(LOG_PATH . DS . 'server.log'));

		if (empty($this->allowedElements) || array_key_exists($class, $this->allowedElements)) {
			$this->childElements[] = $child;
			$log->debug("adding child: " . $class);
		} else {
			$log->debug("Could not add child: " . $class . ", because it is not in allowed list: " . implode(", ", $this->allowedElements));
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

	/**
	 * Parses the name of the class without namespace
	 * 
	 * @param string $name
	 * 		Name of the class
	 */
	private function parseClassname($name) {
		if(strrpos($name, '\\')) {
			return substr($name, strrpos($name, '\\') + 1);
		}
	}
}