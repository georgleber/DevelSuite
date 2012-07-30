<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\view\impl\flexigrid\action;

use DevelSuite\view\impl\dsFlexiGridView;

/**
 * Creates a separator entry in the flexigrid action list
 *
 * @package DevelSuite\view\impl\flexigrid\action
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsFlexiSeparator implements dsIFlexiAction {
	public function setTable(dsFlexiGridView $table) {
		// do nothing
	}

	public function __toString() {
		return "{ separator: true }";
	}

	public function getJSFunction() {
		// not needed
		return "";
	}
}