<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\reflection;

use DevelSuite\reflection\annotation\dsAnnotationParser;
use DevelSuite\reflection\annotations\dsIAnnotationParser;

/**
 * Extends the ReflectionProperty to add support for annotations.
 *
 * @package DevelSuite\reflection
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsReflectionProperty extends \ReflectionProperty implements dsIReflection {
	/**
	 * Array with all annotations of this property
	 * @var array
	 */
	private $annotations = array();

	/**
	 * AnnotationParser for properties
	 * @var dsIAnnotationParser
	 */
	private $parser;

	/**
	 * Constructor
	 *
	 * @param mixed $class
	 * 		Classname or object (instance of the class) that contains the property.
	 * @param string $name
	 * 		Name of the property
	 */
	public function __construct($class, $name) {
		parent::__construct($class, $name);

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
	public function getAnnotation($annotationName) {
		if (empty($this->annotations)) {
			$this->getAnnotations();
		}

		return $this->annotations[$annotationName];
	}

	/**
	 * (non-PHPdoc)
	 * @see DevelSuite\reflection.dsIReflection::hasAnnotation()
	 */
	public function hasAnnotation($annotationName) {
		if (empty($this->annotations)) {
			$this->getAnnotations();
		}

		array_key_exists($annotationName, $this->annotations);
	}

	/**
	 * (non-PHPdoc)
	 * @see DevelSuite\reflection.dsIReflection::setAnnotationParser()
	 */
	public function setAnnotationParser(dsIAnnotationParser $parser) {
		$this->parser = $parser;
	}
}