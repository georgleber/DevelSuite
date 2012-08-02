<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\config;

/**
 * FIXME
 *
 * @package DevelSuite\config
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsConfig {
	/**
	 * Saves all settings
	 * @var array
	 */
	private static $values = array();

	/**
	 * FIXME
	 *
	 * @param string $key
	 * 		Key for the setting
	 * @param mixed $value
	 * 		Value of the setting
	 */
	public static function write($key, $value) {
		if(strpos($key, '.') === FALSE) {
			self::$values[$key] = $value;
		} else {
			$keys = explode('.', $key, 3);
			$count = count($keys);
			switch ($count) {
				case 1:
					self::$values[$keys[0]] = $value;
					break;
				case 2:
					self::$values[$keys[0]][$keys[1]] = $value;
					break;
				case 3:
					self::$values[$keys[0]][$keys[1]][$keys[2]] = $value;
					break;
			}
		}
	}

	/**
	 * FIXME
	 *
	 * @param string $key
	 * 		Key of the setting
	 * @param mixed $fallback
	 * 		Optional fallback if no setting was found under the key
	 */
	public static function read($key = NULL, $fallback = NULL) {
		if ($key === NULL) {
			return self::$values;
		}

		if (isset(self::$values[$key])) {
			return self::$values[$key];
		}

		if (strpos($key, '.') !== FALSE) {
			$keys = explode('.', $key, 3);
			$key = $keys[0];
		}

		if (!isset(self::$values[$key])) {
			return $fallback;
		}

		$count = count($keys);
		switch ($count) {
			case 2:
				if (isset(self::$values[$key][$keys[1]])) {
					return self::$values[$key][$keys[1]];
				}
				break;
			case 3:
				if (isset(self::$values[$key][$keys[1]][$keys[2]])) {
					return self::$values[$key][$keys[1]][$keys[2]];
				}

				if (!isset(self::$values[$key][$keys[1]])) {
					return $fallback;
				}
				break;
		}

		return $fallback;
	}

	/**
	 * Deletes a config entry
	 *
	 * @param string $key
	 * 		Key of setting, which will be removed
	 */
	public static function delete($key) {
		if (strpos($key, '.') === FALSE) {
			unset(self::$values[$key]);
		} else {
			$keys = explode('.', $key, 3);
			self::$values[$keys[0]] = NULL;
		}
	}
}