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
 * Adds and executes the chain of filters, which will
 * be called before or after processing of the controller.
 *
 * @package DevelSuite\controller\filter
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsFilterChain {
	/**
	 * Array with all filters
	 * @var array
	 */
	private $filters = array();

	/**
	 * Register a new filter
	 *
	 * @param dsIFilter $filter
	 * 			The new filter
	 */
	public function addFilter(dsIFilter $filter) {
		$this->filters[] = $filter;
	}

	/**
	 * Executes all filter in the chain
	 */
	public function processFilters() {
		foreach ($this->filters as $filter) {
			$filter->execute();
		}
	}
}