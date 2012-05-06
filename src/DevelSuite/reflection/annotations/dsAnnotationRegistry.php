<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\reflection\annotations;

use DevelSuite\reflection\annotations\annotation\dsAnnotation;

/**
 * Registry for all annotation namespaces.
 * The annotations found by the AnnotationParser will
 * be loaded by this registry.
 *
 * @package DevelSuite\reflection\annotations
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsAnnotationRegistry {
	/**
	 * Registered namespaces to search for annotations
	 * @var array
	 */
	private static $namespaces = array();

	/**
	 * Enter description here ...
	 *
	 * @param string $namespace
	 */
	public static function addNamespace($namespace) {
		self::$namespaces[] = $namespace;
	}

	/**
	 * Searchs for the annotation class on the namespaces and
	 * tries to load it.
	 *
	 * @param string $annotation
	 */
	public static function load($annotationName) {
		foreach (self::$namespaces as $namespace) {
			$class = $namespace . "\\" . $annotationName;
			
			// check if annotation class exists
			if (class_exists($class)) {
				$annotation = new $class();

				// annotation must extend dsAnnotation
				if ($annotation instanceof dsAnnotation) {
					return $annotation;
				} else {
					// FIXME
					throw new \Exception("Annotation must extend dsAnnotation");
				}
			}
		}

		return NULL;
	}
}