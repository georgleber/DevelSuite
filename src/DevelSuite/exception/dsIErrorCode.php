<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\exception;

/**
 * Interface for all ErrorCodes
 *
 * @package DevelSuite\exception
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
interface dsIErrorCode {
	/**
	 * Path of the ini-file
	 */
	public function getFilePath();

	/**
	 * Name of the ini-bundle of the error message
	 */
	public function getBundleName();

	/**
	 * Key for this error message
	 */
	public function getKey();
}