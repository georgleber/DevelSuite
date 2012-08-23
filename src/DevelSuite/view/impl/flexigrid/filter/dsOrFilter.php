<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\view\impl\flexigrid\filter;

/**
 * Used for combining diffenret filters with an or relation.
 *
 * @package DevelSuite\view\impl\flexigrid\filter
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsOrFilter extends dsALogicFilter {
	private $filterList;

	public function addOr(dsIFilter $filter) {
		$this->filterList[] = $filter;
	}

	public function buildQuery() {
		$query = "";
		foreach ($this->filterList as $filter) {
			$query .= "->_or()";
			if ($filter instanceof dsAColumnFilter) {
				$query .= "->where(" . $filter->buildQuery() . ")";
			} else {
				$query .= $filter->buildQuery();
			}
		}

		return $query;
	}
}