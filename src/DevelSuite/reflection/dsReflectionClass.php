<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\reflection;

use DevelSuite\reflection\dsReflectionMethod;
use DevelSuite\reflection\annotations\parser\dsAnnotationParser;
use DevelSuite\reflection\annotations\parser\dsIAnnotationParser;

/**
 * Extends the ReflectionClass to add support for annotations.
 *
 * @package DevelSuite\reflection
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsReflectionClass extends \ReflectionClass implements dsIReflection {
	/**
	 * Annotations of this method
	 * @var array
	 */
	private $annotations = array();

	/**
	 * AnnotationParser for methods
	 * @var dsIAnnotationParser
	 */
	private $parser;

	/**
	 * Constructor
	 *
	 * @param mixed $argument
	 * 		Either a string containing the name of
	 * 		the class to reflect, or an object.
	 */
	public function __construct($argument) {
		parent::__construct($argument);

		$this->parser = new dsAnnotationParser();
	}

	/**
	 * (non-PHPdoc)
	 * @see DevelSuite\reflection.dsIReflection::getAnnotations()
	 */
	public function getAnnotations() {
		if (empty($this->annotations)) {
			$annotations = $this->parser->parse($this->getDocComment());

			foreach ($annotations as $annotation) {
				$this->annotations[$annotation->getName()] = $annotation;
			}
		}

		return $this->annotations;
	}

	/**
	 * (non-PHPdoc)
	 * @see DevelSuite\reflection.dsIReflection::getAnnotation()
	 */
	public function getAnnotation($name) {
		if (empty($this->annotations)) {
			$this->getAnnotations();
		}

		return $this->annotations[$name];
	}

	/**
	 * (non-PHPdoc)
	 * @see DevelSuite\reflection.dsIReflection::getAnnotation()
	 */
	public function hasAnnotation($annotationName) {
		if (empty($this->annotations)) {
			$this->getAnnotations();
		}

		return array_key_exists($annotationName, $this->annotations);
	}

	/**
	 * (non-PHPdoc)
	 * @see DevelSuite\reflection.dsIReflection::setAnnotationParser()
	 */
	public function setAnnotationParser(dsIAnnotationParser $parser) {
		$this->parser = $parser;
	}

	/**
	 * (non-PHPdoc)
	 * @see ReflectionClass::getConstructor()
	 */
	public function getConstructor() {
		return $this->getMethod("__construct");
	}

	/**
	 *
	 * Enter description here ...
	 * @param string $annotationName
	 * 		Name of the searched annotation
	 */
	public function getAnnotatedMethods($annotationName) {
		$annotatedMethods = array();

		$methods = $this->getMethods(\ReflectionMethod::IS_PUBLIC);
		foreach ($methods as $method) {
			if ($method->hasAnnotation($annotationName)) {
				$annotatedMethods[] = $method;
			}
		}

		return  $annotatedMethods;
	}

	/**
	 * Gets a ReflectionMethod
	 *
	 * @return dsReflectionMethod The method or NULL
	 */
	public function getMethod($name) {
		if (parent::getMethod($name) == NULL) {
			return NULL;
		}

		return new dsReflectionMethod($this, $name);
	}

	/**
	 * Gets a list of methods
	 *
	 * @return array An array of dsReflectionMethods
	 */
	public function getMethods($filter = NULL) {
		$methods = array();
		if ($filter == NULL) {
			$methods = parent::getMethods();
		} else {
			$methods = parent::getMethods($filter);
		}

		$reflMethods = array();
		foreach ($methods as $method) {
			$reflMethods[] = new dsReflectionMethod($this, $method->getName());
		}

		return $reflMethods;
	}

	/**
	 * (non-PHPdoc)
	 * @see ReflectionClass::getParentClass()
	 */
	public function getParentClass() {
		$class = parent::getParentClass();

		if ($class == FALSE) {
			return FALSE;
		}

		return new self($class->getName());
	}

	/**
	 * (non-PHPdoc)
	 * @see ReflectionClass::getProperty()
	 */
	public function getProperty($name) {
		if (parent::getProperty($name) == NULL) {
			return NULL;
		}

		return new dsReflectionProperty($this, $name);
	}

	/**
	 * (non-PHPdoc)
	 * @see ReflectionClass::getProperties()
	 */
	public function getProperties($filter) {
		$properties = parent::getProperties($filter);

		$reflProperties = array();
		foreach ($properties as $property) {
			$reflProperties[] = new dsReflectionProperty($this, $property->getName());
		}

		return $reflProperties;
	}

	/**
	 * (non-PHPdoc)
	 * @see ReflectionClass::getStaticProperties()
	 */
	public function getStaticProperties() {
		return $this->getProperties(ReflectionProperty::IS_STATIC);
	}
}