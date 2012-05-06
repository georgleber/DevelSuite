<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\reflection\annotations;

/**
 * Interface for all annotations
 *
 * @package DevelSuite\reflection\annotations
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
interface dsIAnnotation {
	/**
	 * Set the name of the annotation
	 *
	 * @param string $annotationName
	 * 		Name of the annotation
	 */
	public function setName($annotationName);

	/**
	 * @return string name of the annotation
	 */
	public function getName();

	/**
	 * Initialize all attributes of the annotation
	 *
	 * @param array $attributes
	 * 		Array with all attributes of the annotation
	 */
	public function initAttributes($attributes);
}