<?php
/*
 * This file is part of the DevelSuite
* Copyright (C) 2012 Georg Henkel <info@develman.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
namespace DevelSuite\view;

use DevelSuite\dsApp;

/**
 * Interface for all Views
 *
 * @package DevelSuite\view
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
interface dsIView {
	/**
	 * Assign values to the view
	 *
	 * @param string $key
	 * 			Key of the Value
	 * @param mixed $value
	 * 			The value to assign
	 */
	public function assign($key, $value);
	
	/**
	 * Renders the view
	 */
	public function render();
}