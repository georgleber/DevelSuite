<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\grid\renderer\impl;

use DevelSuite\grid\model\dsColumn;
use DevelSuite\grid\renderer\dsICellRenderer;

/**
 * FIXME
 *
 * @package DevelSuite\grid\renderer\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
abstract class dsACellRenderer implements dsICellRenderer {
	protected $column;
	protected $value;

	public function setColumn(dsColumn $column) {
		$this->column = $column;
	}

	public function setValue($value) {
		$this->value = $value;
	}
}