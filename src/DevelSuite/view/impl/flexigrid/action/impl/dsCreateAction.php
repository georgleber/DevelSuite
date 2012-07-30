<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\view\impl\flexigrid\action\impl;

use DevelSuite\view\impl\flexigrid\action\dsFlexiAction;

/**
 * Calls the create function.
 *
 * @package DevelSuite\view\impl\flexigrid\action
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsCreateAction extends dsFlexiAction {
	public function __construct() {
		parent::__construct("Erstellen", "create");
	}
}