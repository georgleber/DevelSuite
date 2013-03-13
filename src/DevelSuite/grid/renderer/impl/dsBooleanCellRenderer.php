<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\grid\renderer\impl;

use DevelSuite\util\dsStringTools;

/**
 * FIXME
 *
 * @package DevelSuite\grid\renderer\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsBooleanCellRenderer extends dsACellRenderer {
	public function setValue($value) {
		$this->value = dsStringTools::isBoolean($value);
	}

	public function render() {
		$image = NULL;
		if ($this->value) {
			$image = dirname(__FILE__) . DS . "res" . DS . "cbx_true.png";
		} else {
			$image = dirname(__FILE__) . DS . "res" . DS . "cbx_false.png";
		}
		
		$code = "<img src='" . $image . "' width='16' />";
		return $code;
	}
}