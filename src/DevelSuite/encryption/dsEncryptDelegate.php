<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2011 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\encryption;

use DevelSuite\encryption\impl\dsBCrypt;
use DevelSuite\encryption\impl\dsSHA1;

/**
 * Delegator class for encryption and check of passwords.
 *
 * @package DevelSuite\encryption
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsEncryptDelegate {
	/**
	 * Delegates the encryption of a password to BCrypt (blowfish) encryption class
	 * if available, otherwise to SHA1 encryption class.
	 *
	 * @param string $password
	 * 			Password, which will be encrypted
	 * @param string $addition
	 * 			Additional information
	 */
	public static function createHash($password, $addition = NULL) {
		// check if blowfish is available and encrypt with BCRYPT (blowfish),
		// otherwise encrypt with SHA1
		$hash = NULL;
		if(self::checkBlowfish()) {
			$hash = dsBCrypt::createHash($password, $addition);
		} else {
			$hash = dsSHA1::createHash($password, $addition);
		}

		return $hash;
	}

	/**
	 * Delegates to the corresponding encryption class. If blowfish is available the
	 * BCrypt encryption class is called to check the given password against the
	 * encrypted hash, otherwise the SHA1 encryption class is called.
	 *
	 * @param string $password
	 * 			The password to check
	 * @param string $hash
	 * 			The encrypted password hash
	 * @param string $addition
	 * 			Additional information for the SHA1 class
	 * @return TRUE, if check of hash against the password was successful
	 */
	public static function checkHash($password, $hash, $addition = NULL) {
		$result = FALSE;
		if(self::checkBlowfish()) {
			$result = dsBCrypt::checkHash($password, $hash);
		} else {
			$result = dsSHA1::checkHash($password, $hash, $addition);
		}

		return $result;
	}

	/**
	 * Prueft ob der Blowfish Algorithmus unterstuetzt wird
	 *
	 * @return TRUE, if blowfish is available or not
	 */
	private static function checkBlowfish() {
		if (CRYPT_BLOWFISH !== 1) {
			return FALSE;
		}

		return TRUE;
	}
}