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
 * FIXME
 *
 * @package DevelSuite\view\impl\flexigrid\filter
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
interface dsIColumnFilter {
	public function getColumn();
	public function getQuery();
	public function getComparisonType();
}