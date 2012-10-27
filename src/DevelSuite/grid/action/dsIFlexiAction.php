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
 * Interface for all FlexiActions
 *
 * @package DevelSuite\grid\action
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
interface dsIFlexiAction {
	/**
	 * Return the identifier for this action
	 */
	public function getIdentifier();
	
	/**
	 * Creates code for a javascript function depending on this action and its needs.
	 */
	public function getJSFunction();

	/**
	 * Provide javascript representation of this button
	 */
	public function __toString();
}