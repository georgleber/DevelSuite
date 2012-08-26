<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form\validator;

/**
 * This chain holds Validators for validation of form elemens.
 *
 * @package DevelSuite\form\element
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
use Monolog\Handler\StreamHandler;

use Monolog\Logger;

class dsValidatorChain {
	/**
	 * List of all validators
	 * @var array
	 */
	private $validators = array();

	/**
	 * Add a new validator
	 *
	 * @param dsAValidator $validator
	 * 			An additional validator
	 */
	public function addValidator(dsAValidator $validator) {
		$this->validators[] = $validator;
	}

	/**
	 * Run all validators
	 *
	 * @return TRUE, if all validators run without errors.
	 */
	public function processValidator() {
		$validationResult = TRUE;
		
		// process validators
		foreach ($this->validators as $validator) {
			$result = $validator->validate();
			if ($result == FALSE) {
				$validationResult = FALSE;
				break;
			}
		}

		return $validationResult;
	}
}