<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\encryption\impl;

use DevelSuite\exception\impl\dsEncryptionException;

/**
 * FIXME
 *
 * @package DevelSuite\encryption
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsBCrypt implements dsIEncrypt {
	// Konstante mit erlaubten Zeichen fuer den Passwortzusatz
	const SALTCHARS = './0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	// Anzahl der Wiederholungen (Cost). minimal 4, maximal 31
	public static $cost = 12;

	/**
	 * Erzeugt einen Hash aus einem Passwort
	 *
	 * @param string $password
	 * 			Das zu verschluesselnde Passwort
	 * @param int  $cost
	 * 			Anzahl der Wiederholungen
	 * @return string
	 */
	public static function createHash($password, $cost = NULL) {
		// Pruefen ob Blowfish vom Server unterstuetzt wird
		self::checkBlowfish();

		// 22-stelligen Salt erzeugen der fuer Blowfish erforderlich ist
		$tmpSalt = '';
		for ($i = 0; $i <= 21; $i++) {
			$tmpStr = str_shuffle(self::SALTCHARS);
			$tmpSalt .= $tmpStr[0];
		}

		// Anzahl der Wiederholungen ermitteln
		$useCost = 0;
		if ($cost) {
			// Fuehrende 0 voranstellen bei einstelliger Wiederholung
			$useCost = sprintf('%02d', min(31, max(intval($cost), 4)));
		} else {
			// Standard verwenden
			$useCost = self::$cost;
		}

		// Komplettes Salt mit Algorithmus und Wiederholungen
		$salt = '$2a$'.$useCost.'$'.$tmpSalt.'$';

		// Passwort Hash erzeugen
		$hash = crypt($password, $salt);

		return $hash;
	}

	public static function checkHash($password, $hash, $unused = NULL) {
		// Pruefen ob Blowfish vom Server unterstuetzt wird
		self::checkBlowfish();

		// Komplettes Salt mit Algorithmus und Wiederholungen aus dem Hash extrahieren
		$tmpSalt = substr($hash, 0, 29);

		// Vergleichshash erzeugen
		$tmpHash = crypt($password, $tmpSalt.'$');

		// Stimmt das Passwort mit dem Hash ueberein ist der Rueckgabewert TRUE, ansonsten FALSE
		return ($tmpHash == $hash) ? TRUE : FALSE;
	}

	/**
	 * Prueft ob der Blowfish Algorithmus unterstuetzt wird
	 */
	private static function checkBlowfish() {
		if (CRYPT_BLOWFISH !== 1) {
			throw new dsEncryptionException(dsEncryptionException::BLOWFISH_UNSUPPORTED);
		}
	}
}