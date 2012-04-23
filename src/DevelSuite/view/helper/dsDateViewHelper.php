<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\view\helper;

/**
 * ViewHelper to handle date operations.
 *
 * @package DevelSuite\view\helper
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsDateViewHelper implements dsIViewHelper {
	/**
	 * Calculate difference off a datetime to current time
	 * and format it in words (e.g. 1 hour, 2 days, ...)
	 * Enter description here ...
	 * @param unknown_type $time
	 */
	public function diffTime($time) {
		$todaydate = date("Y-m-d H:i:s");

		$ago = strtotime($todaydate) - strtotime($time);
		if ($ago >= 86400) {
			$diff = floor($ago/86400).' days';
		} elseif ($ago >= 3600) {
			$diff = floor($ago/3600).' hours';
		} elseif ($ago >= 60) {
			$diff = floor($ago/60).' minutes';
		} else {
			$diff = $ago.' seconds';
		}

		return $diff;
	}
}