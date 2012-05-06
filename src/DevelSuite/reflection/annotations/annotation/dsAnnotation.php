<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\reflection\annotation;

use DevelSuite\reflection\annotations\dsIAnnotation;

/**
 * FIXME
 *
 * @package DevelSuite\reflection\annotation
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsAnnotation implements dsIAnnotation {
	private $annotationName;

	/**
	 * (non-PHPdoc)
	 * @see DevelSuite\reflection\annotations.dsIAnnotation::setAnnotationName()
	 */
	public function setName($annotationName) {
		$this->annotationName = $annotationName;
	}

	/**
	 * (non-PHPdoc)
	 * @see DevelSuite\reflection\annotations.dsIAnnotation::getAnnotationName()
	 */
	public function getAnnotationName() {
		return $this->annotationName;
	}

}