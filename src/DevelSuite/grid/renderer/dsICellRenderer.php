<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\grid\renderer;

use DevelSuite\grid\model\dsColumn;

/**
 * FIXME
 *
 * @package DevelSuite\grid\renderer
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
interface dsICellRenderer {
	public function setColumn(dsColumn $column);
	public function setValue($value);
	public function render();
}