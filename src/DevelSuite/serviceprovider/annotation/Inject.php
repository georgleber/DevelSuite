<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\reflection\annotations\annotation;

/**
 * Inject claims to inject the method or property by
 * the dsServiceProvider.
 *
 * @package DevelSuite\reflection\annotations\annotation
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class Inject extends dsAnnotation {
	/**
	 * (non-PHPdoc)
	 * @see DevelSuite\reflection\annotations\annotation.dsAnnotation::initAttributes()
	 */
	public function initAttributes(array $attributes) {}
}