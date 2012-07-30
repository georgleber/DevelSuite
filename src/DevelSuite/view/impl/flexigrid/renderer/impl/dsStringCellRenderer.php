<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\view\impl\flexigrid\renderer\impl;

use DevelSuite\util\dsStringTools;

/**
 * FIXME
 *
 * @package DevelSuite\view\impl\flexigrid\renderer\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsStringCellRenderer extends dsACellRenderer {
	public function setValue($value) {
		$width = $this->column->getWidth();
		$value = (string) $value;

		if (dsStringTools::isNullOrEmpty($value)) {
			$this->value = "";
		} else {
			if (mb_strwidth($value) > $width) {
				$this->value = substr($value, 0, ($width / 6)) . " ...";
			} else {
				$this->value = $value;
			}
		}
	}

	public function render() {
		return $this->value;
	}
}