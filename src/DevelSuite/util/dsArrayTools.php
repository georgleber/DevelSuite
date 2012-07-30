<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\util;

/**
 * Helper class for array relevant operations
 *
 * @package DevelSuite\util
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsArrayTools {
	public static function arrayInsert(array $array, $index, $value) {
		$preArr = array_slice($array, 0, $index);
		$postArr = array_slice($array, $index);

		$mergedArr = array();
		if (is_array($value)) {
			$mergedArr = array_merge($preArr, $value, $postArr);
		} else {
			$mergedArr = array_merge($preArr, array($value), $postArr);
		}

		return $mergedArr;
	}

	public static function arrayRemove(array $array, $index) {
		$preArr = array_slice($array, 0, $index);
		$postArr = array_slice($array, $index + 1);

		return array_merge($preArr, $postArr);
	}
}