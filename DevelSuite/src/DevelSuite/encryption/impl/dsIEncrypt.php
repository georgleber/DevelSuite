<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2011 Georg Henkel <info@develman.de>
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
interface dsIEncrypt {
	public static function createHash($password, $specificAddition = NULL);

	/**
	 * Prueft ein Passwort gegen einen Hash
	 *
	 * @param string  	$password Passwort aus dem der zu vergleichende Hash erzeugt werden soll
	 * @param string	$hash Der zu vergleichende Hash mit komplettem Salt
	 * @param mixed		$unused NOT USED - ONLY NEEDED FOR INTERFACE NEEDS (see dsSHA1.php)!!!
	 * @return bool
	 */
	public static function checkHash($password, $hash);
}