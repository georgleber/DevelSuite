<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\reflection;

use DevelSuite\reflection\annotations\parser\dsIAnnotationParser;

/**
 * Interface for all extended ReflectionObjects to allow other
 * Parser than the default dsAnnotationParser
 *
 * @package DevelSuite\reflection
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
interface dsIReflection {
	/**
	 * Set an AnnotationParser
	 *
	 * @param dsIAnnotationParser $parser
	 * 		AnnotationParser for the ReflectionObject
	 */
	public function setAnnotationParser(dsIAnnotationParser $parser);

	/**
	 * Retrieve all annotations of this object
	 *
	 * @return array All found annotations
	 */
	public function getAnnotations();
	
	/**
	 * Get an annotation of this method
	 *
	 * @param string $annotationName
	 * 		Name of the annotation
	 *
	 * @return dsAnnotation If exists the found annotation or NULL
	 */
	public function getAnnotation($annotationName);
}