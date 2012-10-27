<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\grid\action;

use DevelSuite\grid\view\dsFlexiGridView;

/**
 * Creates a separator entry in the flexigrid action list
 *
 * @package DevelSuite\grid\action
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsFlexiSeparator implements dsIFlexiAction {
	/**
	 * Identifier for this action
	 * @var string
	 */
	private $identifier;

	/**
	 * Constructor
	 *
	 * @param string $identifier
	 * 		Identifier for this action
	 */
	public function __construct($identifier) {
		$this->identifier = $identifier;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\grid\action.dsIFlexiAction::getIdentifier()
	 */
	public function getIdentifier() {
		return $this->identifier;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\grid\action.dsIFlexiAction::getJSFunction()
	 */
	public function getJSFunction() {
		// not needed
		return "";
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\grid\action.dsIFlexiAction::__toString()
	 */
	public function __toString() {
		return "{ separator: true }";
	}
}