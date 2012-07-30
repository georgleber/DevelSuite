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
 * FIXME
 *
 * @package DevelSuite\view\impl\flexigrid\action
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
interface dsIFlexiAction {
	public function setTable(dsFlexiGridView $table);
	public function getJSFunction();
	public function __toString();
}