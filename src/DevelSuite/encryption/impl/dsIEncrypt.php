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
 * Interface for all encryption classes
 *
 * @package DevelSuite\encryption
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
interface dsIEncrypt {
	/**
	 * Constant with all allowed characters for password salt
	 */
	const SALTCHARS = './0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	/**
	 * Creates a hash from a password
	 *
	 * @param string $password
	 * 		Password to hash
	 * @param string $specificAddition
	 * 		Algorithm specific additional information (optional)
	 * @return string
	 * 		The hashed password
	 */
	public static function createHash($password, $specificAddition = NULL);

	/**
	 * Checks a password against a hash
	 *
	 * @param string $password
	 * 		Password to check
	 * @param string $hash
	 * 		Hash for comparison with hashed password
	 * @return bool
	 * 		TRUE, if hashed password is equal to hash
	 */
	public static function checkHash($password, $hash, $specificAddition = NULl);
}