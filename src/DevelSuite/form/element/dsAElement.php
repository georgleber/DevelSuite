<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form\element;

use DevelSuite\dsApp;

/**
 * Abstract Superclass for all form elements.
 *
 * @package DevelSuite\form\element
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
abstract class dsAElement {
	/**
	 * Name of the element
	 * @var string
	 */
	protected $name;

	/**
	 * Caption of the element
	 * @var string
	 */
	protected $caption;

	/**
	 * Is this element mandatory
	 * @var bool
	 */
	protected $mandatory;

	/**
	 * Is the element disabled
	 * @var bool
	 */
	protected $disabled;

	/**
	 * Tab index of the element (index has to be > 0)
	 * @var int
	 */
	protected $tabIndex;

	/**
	 * Additional css classes for the element
	 * @var array
	 */
	protected $cssClasses = array();

	/**
	 * Constructor
	 *
	 * @param string $caption
	 * 			Caption of this element
	 * @param string $name
	 * 			Name of this element
	 */
	public function __construct($caption, $name) {
		$this->caption = $caption;
		$this->name = $name;
	}

	/**
	 * Set this element mandatory
	 *
	 * @param bool $mandatory
	 * 			TRUE, if element is mandatory
	 */
	public function setMandatory($mandatory = TRUE) {
		$this->mandatory = $mandatory;
		return $this;
	}

	/**
	 * Returns if this element is mandatory
	 */
	public function isMandatory() {
		return $this->mandatory;
	}

	/**
	 * Set the element disabled
	 *
	 * @param bool $disabled
	 * 			TRUE, if the element should be disabled
	 */
	public function setDisabled($disabled = TRUE) {
		$this->disabled = $disabled;
		return $this;
	}

	/**
	 * Set a tabIndex for this element
	 *
	 * @param int $tabIndex
	 * 			TabIndex for this element
	 */
	public function setTabIndex($tabIndex) {
		if ($tabIndex <= 0) {
			return $this;
		}

		$this->tabIndex = $tabIndex;
		return $this;
	}

	/**
	 * Set a CSS class for this element.
	 *
	 * @param string $class
	 * 			CSS class name for this element
	 */
	public function addCssClass($class) {
		$this->cssClass[] = $class;
		return $this;
	}

	/**
	 * Returns the value of this element.
	 */
	public function getValue() {
		$result = NULL;
		$request = dsApp::getRequest();

		if (isset($request[$this->name])) {
			$result = $request[$this->name];
		}

		return $result;
	}

	/**
	 * Populates the form data after an unseccessfull commit
	 */
	abstract protected function populate();

	/**
	 * Build up the HTML of the elements
	 */
	abstract protected function buildHTML();
}