<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2011 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\core\form\element\validator;

use DevelSuite\core\form\element\dsAElement;
use DevelSuite\core\form\dsForm;

/**
 * This chain holds Validators for validation of form elemens.
 *
 * @package DevelSuite\core\form\element\validator
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsValidatorChain {
	private $validators = array();

	/**
	 * Add a new validator
	 *
	 * @param dsAValidator $validator
	 * 			The new validator
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
		$retVal = TRUE;
		foreach ($this->validators as $validator) {
			$result = $validator->validate();
			if (!$result) {
				$retVal = FALSE;
				break;
			}
		}

		return $retVal;
	}
}