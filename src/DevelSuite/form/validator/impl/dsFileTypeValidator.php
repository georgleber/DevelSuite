<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form\validator\impl;

use DevelSuite\i18n\dsResourceBundle;
use DevelSuite\form\validator\dsAValidator;
use DevelSuite\util\dsStringTools;

/**
 * Validator for FileInput elements.
 *
 * @package DevelSuite\form\validator\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsFileTypeValidator extends dsAValidator {
	/**
	 * List of all allowed mime types for upload
	 * @var array
	 */
	private $allowedTypes = array();

	/**
	 * Set allowed filetypes to upload
	 *
	 * @param array $fileTypes
	 * 		Filetypes, which are allowed to upload
	 */
	public function setAllowedTypes(array $mimeTypes) {
		$this->allowedTypes = $mimeTypes;

		$iniArr = dsResourceBundle::getBundle(dirname(__FILE__), "validation");
		$this->errorMessage = sprintf($iniArr['dsFileValidator.error'], implode(",", array_keys($this->allowedTypes)));
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\form\validator.dsAValidator::validateElement()
	 */
	public function validateElement() {
		$result = TRUE;
		$file = $this->element->getValue();

		if ($file != NULL && $file['error'] == UPLOAD_ERR_OK) {
			// check mime type
			$filemime = $file['type'];
			$filename = $file['name'];
			$fileext = $this->getExtension($filename);

			$typeCheck = FALSE;
			foreach ($this->allowedTypes as $singleTypes) {
				foreach ($singleTypes as $type => $mimeType) {
					if ($type === $fileext && $mimeType === $filemime) {
						$typeCheck = TRUE;
						break;
					}
				}

				if ($typeCheck === TRUE) break;
			}

			$result = $typeCheck;
		}

		return $result;
	}

	/**
	 * Retrieve the extension of a file name
	 *
	 * @param string $fileName
	 * 		Name of the file
	 */
	private function getExtension($fileName) {
		if(strrpos($fileName, '.')) {
			return substr($fileName, strrpos($fileName, '.') + 1);
		}

		return FALSE;
	}
}