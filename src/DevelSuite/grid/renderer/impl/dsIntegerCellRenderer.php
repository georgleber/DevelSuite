<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\grid\renderer\impl;

/**
 * FIXME
 *
 * @package DevelSuite\grid\renderer\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsIntegerCellRenderer extends dsACellRenderer {
	public function render() {
		return $this->value;
	}
}