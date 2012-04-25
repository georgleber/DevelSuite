<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\exception;

use DevelSuite\exception\dsIErrorCode;
use DevelSuite\exception\spl\dsNullPointerException;
use DevelSuite\util\dsStringTools;

/**
 * Defining the key in an ini-bundle,
 * where the error message can be found.
 *
 * @package DevelSuite\exception
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsErrorCode implements dsIErrorCode {
	/**
	 * FilePath of the ini-file
	 * @var string
	 */
	private $filePath;
	/**
	 * Name of the ini-bundle
	 * @var string
	 */
	private $bundleName;

	/**
	 * Key in the ini-file
	 * @var string
	 */
	private $key;

	/**
	 * Constructor
	 *
	 * @param string $filePath
	 * 		Path to the error bundle
	 * @param string $bundleName
	 * 		Name of the bundle in which the error message are saved
	 * @param string $key
	 * 		Key of the error message
	 */
	public function __construct($filePath, $bundleName, $key) {
		if (dsStringTools::isNullOrEmpty($filePath)) {
			throw new dsNullPointerException("filePath is null");
		}

		if (dsStringTools::isNullOrEmpty($bundleName)) {
			throw new dsNullPointerException("bundleName is null");
		}

		if(dsStringTools::isNullOrEmpty($key)) {
			throw new dsNullPointerException("key is null");
		}

		$this->filePath = $filePath;
		$this->bundleName = $bundleName;
		$this->key = $key;
	}

	/**
	 * (non-PHPdoc)
	 * @see DevelSuite\exception.dsIErrorCode::getFilePath()
	 */
	public function getFilePath() {
		return $this->filePath;
	}

	/**
	 * (non-PHPdoc)
	 * @see DevelSuite\exception.dsIErrorCode::getSection()
	 */
	public function getBundleName() {
		return $this->bundleName;
	}

	/**
	 * (non-PHPdoc)
	 * @see DevelSuite\exception.dsIErrorCode::getKey()
	 */
	public function getKey() {
		return $this->key;
	}
}