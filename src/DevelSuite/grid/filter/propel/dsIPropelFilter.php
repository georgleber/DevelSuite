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
 * Common interface for all propel-related FlexiGrid filters
 *
 * @package DevelSuite\grid\filter\propel
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
interface dsIPropelFilter extends dsIFilter {
	/**
	 * Create the query of this chained filters
	 *
	 * @param QueryClass $queryClass
	 * 		The query class to load data with propel
	 */
	public function buildQuery($queryClass);
}