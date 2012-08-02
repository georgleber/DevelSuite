<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\encryption\impl;

use DevelSuite\encryption\dsEncryptDelegate;
use DevelSuite\exception\impl\dsEncryptionException;

/**
 * Class for encryption with Blowfish algorithm 
 *
 * @package DevelSuite\encryption
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsBCrypt implements dsIEncrypt {
	/**
	 * Count of iteration (cost), minimum 4, maximum 31
	 */
	public static $cost = 12;

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\encryption.dsIEncrypt::createHash()
	 */
	public static function createHash($password, $specificAddition = NULL) {
		// check if blowfish is installed
		if (!dsEncryptDelegate::checkBlowfish()) {
			throw new dsEncryptionException(dsEncryptionException::BLOWFISH_UNSUPPORTED);
		}

		// create 22-digit salt, need by Blowfish
		$tmpSalt = '';
		for ($i = 0; $i <= 21; $i++) {
			$tmpStr = str_shuffle(dsIEncrypt::SALTCHARS);
			$tmpSalt .= $tmpStr[0];
		}

		// Anzahl der Wiederholungen ermitteln
		$useCost = 0;
		if ($specificAddition != NULL) {
			// prepend leading 0 at one-digit iteration
			$useCost = sprintf('%02d', min(31, max(intval($specificAddition), 4)));
		} else {
			// use standard
			$useCost = self::$cost;
		}

		// complete salt with algorithm and iterations
		$salt = '$2a$' . $useCost . '$' . $tmpSalt . '$';

		// create password hash
		$hash = crypt($password, $salt);

		return $hash;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\encryption.dsIEncrypt::checkHash()
	 */
	public static function checkHash($password, $hash, $specificAddition = NULl) {
		// check if blowfish is installed
		if (!dsEncryptDelegate::checkBlowfish()) {
			throw new dsEncryptionException(dsEncryptionException::BLOWFISH_UNSUPPORTED);
		}

		// extract complete salt with algorithm and iterations from hash
		$tmpSalt = substr($hash, 0, 29);

		// create hash for comparison
		$tmpHash = crypt($password, $tmpSalt.'$');

		// check hashed password with hash
		return ($tmpHash == $hash) ? TRUE : FALSE;
	}
}