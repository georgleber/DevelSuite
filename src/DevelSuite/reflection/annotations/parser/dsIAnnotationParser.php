<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\reflection\annotations\parser;

/**
 * Interface for all AnnotationParser to allow user defined parser.
 *
 * @package DevelSuite\reflection\annotations\parser
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
interface dsIAnnotationParser {
	/**
	 * Parse the doc comment for annotations
	 *
	 * @param string $docComment
	 * 		The doc comment of the reflection object
	 */
	public function parse($docComment);
}