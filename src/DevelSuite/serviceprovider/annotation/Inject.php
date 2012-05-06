<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\serviceprovider\annotation;

use DevelSuite\reflection\annotations\annotation\dsAnnotation;

/**
 * Inject claims to inject the method or property by
 * the dsServiceProvider.
 *
 * @package DevelSuite\serviceprovider\annotation
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class Inject extends dsAnnotation {
	const NAME = "Inject";

	/**
	 * (non-PHPdoc)
	 * @see DevelSuite\reflection\annotations\annotation.dsAnnotation::getName()
	 */
	public function getName() {
		return self::NAME;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see DevelSuite\reflection\annotations\annotation.dsAnnotation::initAttributes()
	 */
	public function initAttributes(array $attributes) {

	}
}