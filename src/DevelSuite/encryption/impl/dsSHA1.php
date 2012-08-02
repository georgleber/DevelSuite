<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\encryption\impl;

/**
 * Class for encryption with SHA1 algorithm
 *
 * @package DevelSuite\encryption
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsSHA1 implements dsIEncrypt {
	/**
	 * Length of salt
	 */
	public static $saltLength = 16;

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\encryption.dsIEncrypt::createHash()
	 */
	public static function createHash($password, $specificAddition = NULL) {
		// create 16-digit salt, needed by SHA1 algorithm
		$useSalt = 0;
		if($specificAddition != NULL) {
			$useSalt = $specificAddition;
		} else {
			$useSalt = self::$saltLength;
		}

		$salt = '';
		for ($i = 0; $i < $useSalt; $i++) {
			$tmpStr = str_shuffle(dsIEncrypt::SALTCHARS);
			$salt .= $tmpStr[0];
		}

		// create password hash
		$hash = $salt.sha1($salt.$password);

		return $hash;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\encryption.dsIEncrypt::checkHash()
	 */
	public static function checkHash($password, $hash, $specificAddition = NULl) {
		// extract complete salt from hash
		$salt = NULL;
		if($specificAddition == NULL) {
			$salt = substr($hash, 0, 15);
		} else {
			$salt = substr($hash, 0, $specificAddition);
		}

		// create hash for comparison
		$tmpHash = $salt.sha1($salt.$password);

		// check hashed password with hash
		return ($tmpHash == $hash) ? TRUE : FALSE;
	}
}