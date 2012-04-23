<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2011 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\controller\filter;

/**
 * Filter must implement this interface in order to be processed
 * before or after dispatching the controller.
 *
 * @package DevelSuite\controller\filter
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
interface dsIFilter {
	/**
	 * Executes the filter
	 */
	public function execute();
}