<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\view\impl\flexigrid\renderer\impl;

use \DateTime as DateTime;
use \DateTimeZone as DateTimeZone;

/**
 * FIXME
 *
 * @package DevelSuite\view\impl\flexigrid\renderer\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsDateCellRenderer extends dsACellRenderer {
	private $format = 'd.m.Y H:i:s';

	public function setValue($value) {
		if ($value === NULL || $value === '') {
			$this->value = NULL;
		} else if ($value instanceof DateTime) {
			$this->value = $value;
		} else if($this->isTimestamp($value)) {
			$this->value = new DateTime('@' . $value, new DateTimeZone('UTC'));
			// timezone must be explicitly specified and then changed
			// because of a DateTime bug: http://bugs.php.net/bug.php?id=43003
			$this->value->setTimeZone(new DateTimeZone(date_default_timezone_get()));
		} else {
			// stupid DateTime constructor signature
			$this->value = new DateTime($value);
		}
	}

	public function render() {
		if ($this->value === NULL) {
			return "";
		}
		
		if (strpos($this->format, '%') !== FALSE) {
			return strftime($this->format, $this->value->format('U'));
		} else {
			return $this->value->format($this->format);
		}
	}
	
	public function setFormat($format) {
		$this->format = $format;
	}

	private function isTimestamp($value)
	{
		if (!is_numeric($value)) {
			return false;
		}

		$stamp = strtotime($value);

		if (false === $stamp) {
			return true;
		}

		$month = date('m', $value);
		$day   = date('d', $value);
		$year  = date('Y', $value);

		return checkdate($month, $day, $year);
	}
}