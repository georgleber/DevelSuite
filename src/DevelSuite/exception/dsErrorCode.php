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

/**
 * FIXME
 *
 * @package DevelSuite\exception
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsErrorCode implements dsIErrorCode {
	private $filePath;
	private $section;
	private $key;

	public function __construct($filePath, $section, $key) {
		if (!isset($filePath)) {
			throw new dsNullPointerException("filePath is null");
		}

		if (!isset($section)) {
			throw new dsNullPointerException("section is null");
		}

		if(!isset($key)) {
			throw new dsNullPointerException("key is null");
		}

		$this->filePath = $filePath;
		$this->section = $section;
		$this->key = $key;
	}

	public function getFilePath() {
		return $this->filePath;
	}

	public function getSection() {
		return $this->section;
	}

	public function getKey() {
		return $this->key;
	}

	public function toString() {
		return "section=[" + $this->section + "], key=[" + $this->key + "]";
	}
}