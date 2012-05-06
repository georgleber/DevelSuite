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
 * Abstract super class of all Annotations.
 *
 * @package DevelSuite\reflection\annotations\annotation
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
abstract class dsAnnotation {
	/**
	 * Name of the annotation
	 * @var string
	 */
	protected $annotationName;

	/**
	 * Set the name of the annotation
	 *
	 * @param string $annotationName
	 * 		Name of this annotation
	 */
	public function setName($annotationName) {
		$this->annotationName = $annotationName;
	}

	/**
	 * @return string Name of the annotation
	 */
	abstract public function getName();

	/**
	 * Initialize all attributes of the annotation
	 *
	 * @param array $attributes
	 * 		Array with all attributes of the annotation
	 */
	abstract public function initAttributes(array $attributes);
}