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
 * FIXME
 *
 * @package DevelSuite\encryption
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsSHA1 implements dsIEncrypt {
	// Konstante mit erlaubten Zeichen für den Passwortzusatz
	const SALTCHARS = './0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	// Laenge des zu verwenden Salts
	public static $saltLength = 12;
	
	/**
	 * Erzeugt einen Hash aus einem Passwort
	 *
	 * @param string  $password Passwort
	 * @param int  $saltLength Anzahl Stellen fuer den Salt
	 * @return array
	 */
	public static function createHash($password, $saltLength = NULL) {
		// 16-stelligen Salt erzeugen der fuer SHA1 erforderlich ist
		$useSalt = 0;
		if($saltLength) {
			$useSalt = $saltLength;
		} else {
			$useSalt = self::$saltLength;
		}

		$salt = '';
		for ($i = 0; $i < $useSalt; $i++) {
			$tmpStr = str_shuffle(self::SALTCHARS);
			$salt .= $tmpStr[0];
		}

		// Passwort Hash erzeugen
		$hash = $salt.sha1($salt.$password);

		return $hash;
	}

	/**
	 * Prueft ein Passwort gegen einen Hash
	 *
	 * @param string  $password Passwort aus dem der zu vergleichende Hash erzeugt werden soll
	 * @param string  $hash Der zu vergleichende Hash mit komplettem Salt
	 * @return bool
	 */
	public static function checkHash($password, $hash, $saltLength = NULL) {
		$salt = NULL;
		if($saltLength == NULL) {
			// Komplettes Salt aus dem Hash extrahieren
			$salt = substr($hash, 0, 15);
		} else {
			// Komplettes Salt aus dem Hash extrahieren} else {
			$salt = substr($hash, 0, $saltLength);
		}

		// Vergleichshash erzeugen
		$tmpHash = $salt.sha1($salt.$passwort);

		// Stimmt das Passwort mit dem Hash ueberein ist der Rueckgabewert TRUE, ansonsten FALSE
		return ($tmpHash == $hash) ? TRUE : FALSE;
	}
}