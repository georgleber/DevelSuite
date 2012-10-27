<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\grid\filter\propel;

use DevelSuite\grid\filter\dsIFilter;

/**
 * Used for combining diffenret filters with an or relation.
 *
 * @package DevelSuite\grid\filter\propel
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsOrFilter extends dsALogicFilter {
	/**
	 * List of all or related filters
	 * @var array
	 */
	private $filterList = array();

	/**
	 * Add a filter with an or relation
	 *
	 * @param dsIFilter $filter
	 * 		The filter to combine
	 */
	public function addOr(dsIFilter $filter) {
		$this->filterList[] = $filter;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\grid\filter\propel.dsIPropelFilter::buildQuery()
	 */
	public function buildQuery($queryClass) {
		foreach ($this->filterList as $filter) {
			$queryClass->_or();
			$filter->buildQuery($queryClass);
		}
	}
}