<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2011 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\core\form\element\validator\impl;

use DevelSuite\core\logging\dsLogger;

use DevelSuite\core\logging\dsLoggerFactory;

use DevelSuite\core\form\element\impl\dsCheckbox;

use DevelSuite\core\form\element\impl\dsRadioButton;

use DevelSuite\core\form\element\impl\dsCheckboxGroup;

use DevelSuite\core\form\element\impl\dsSelect;

use DevelSuite\core\form\element\impl\dsRadioButtonGroup;

use DevelSuite\core\form\element\dsCompositeElement;

use DevelSuite\core\util\dsStringTools;

use DevelSuite\core\i18n\dsResourceBundle;
use DevelSuite\core\form\element\validator\dsAValidator;

/**
 * Validator for email elements.
 *
 * @package DevelSuite\core\form\element\validator\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsFileTypeValidator extends dsAValidator {
	private $allowedTypes = array();

	/**
	 * Set allowed filetypes to upload
	 *
	 * @param array $fileTypes
	 * 		Filetypes, which are allowed to upload
	 */
	public function setAllowedTypes(array $mimeTypes) {
		$this->allowedTypes = $mimeTypes;
		$iniArr = dsResourceBundle::getBundle(dirname(__FILE__) . DS . "validation");
		
		$extTypes = array();
		foreach ($this->allowedTypes as $singleTypes) {
			foreach ($singleTypes as $type => $mimeTypes) {
				$extTypes[] = $type;
			}
		}
		$this->errorMessage = sprintf($iniArr['dsFileValidator.error'], implode(",", $extTypes));
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\core\form\element\validator.dsAValidator::validate()
	 */
	public function validateElement() {
		$result = TRUE;
		$file = $this->element->getValue();

		if ($file != NULL && $file['error'] == UPLOAD_ERR_OK) {
			// check mime type
			$fileType = $file['type'];
			$filename = $file['name'];
			$fileext = $this->getExtension($filename);

			$extCheck = FALSE;
			$mimeCheck = FALSE;
			foreach ($this->allowedTypes as $singleTypes) {
				foreach ($singleTypes as $type => $mimeTypes) {
					if (!$extCheck) {
						if ($type == $fileext) {
							$extCheck = TRUE;
						}
					}

					if(!$mimeCheck) {
						if (in_array($fileType, $mimeTypes)) {
							$mimeCheck = TRUE;
							break;
						}
					}
				}

				if ($mimeCheck && $extCheck) {
					break;
				}
			}

			if (!$mimeCheck || !$extCheck) {
				$result = FALSE;
			}

		}

		return $result;
	}

	private function getExtension($fileName) {
		if(strrpos($fileName, '.')) {
			return substr($fileName, strrpos($fileName, '.') + 1);
		}

		return FALSE;
	}
}