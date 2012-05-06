<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\reflection;

use DevelSuite\reflection\annotations\parser\dsAnnotationParser;
use DevelSuite\reflection\annotations\parser\dsIAnnotationParser;

/**
 * Extends the ReflectionMethod to add support for annotations.
 *
 * @package DevelSuite\reflection
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsReflectionMethod extends \ReflectionMethod implements dsIReflection {
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
	 * @param mixed $class_or_method
	 * 		Classname or object (instance of the class) that contains the method.
	 * @param string $name
	 * 		Name of the method
	 */
	public function __construct($class, $name) {
		$className = $class;
		if ($class instanceof dsReflectionClass) {
			$className = $class->getName();
		}
		parent::__construct($className, $name);

		$this->parser = new dsAnnotationParser();
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
	 * @see DevelSuite\reflection.dsIReflection::hasAnnotation()
	 */
	public function hasAnnotation($annotationName) {
		if (empty($this->annotations)) {
			$this->getAnnotations();
		}
		
		return array_key_exists($annotationName, $this->annotations);
	}
}