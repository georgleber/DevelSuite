<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\reflection\annotations\parser;

use DevelSuite\reflection\annotations\dsAnnotationRegistry;

/**
 * This class parses the doc comment of a php object for registered annotations.
 *
 * @package DevelSuite\reflection\annotations\parser
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsAnnotationParser implements dsIAnnotationParser {
	/**
	 * All ignored annotations
	 * @var array
	 */
	private static $ignoredAnnotations = array(
		"abstract", "access", "author", "copyright", 
		"deprecated", "deprec", "example", "exception",
		"global", "ignore", "internal", "link", "name",
		"magic", "package", "param", "return", "see",
		"since", "static", "staticvar", "subpackage", 
		"throws", "todo", "var", "version");

	/**
	 * Overwrites the array of ignored annotations
	 *
	 * @param array $ignoredAnnotations
	 * 		Array with the annotations to ignore
	 */
	public function setIgnoredAnnotations(array $ignoredAnnotations) {
		self::$ignoredAnnotations = $ignoredAnnotations;
	}

	/**
	 * (non-PHPdoc)
	 * @see DevelSuite\reflection\annotations\parser.dsIAnnotationParser::parse()
	 */
	public function parse($docComment) {
		$parsedAnnotations = array();

		// no comment exists
		if (trim($docComment) == '') {
			return $parsedAnnotations;
		}

		// extract the content of comment without * and /
		$docComment = preg_replace('#[ \t]*(?:\/\*\*|\*\/|\*)?[ ]{0,1}(.*)?#', '$1', $docComment);
		$docComment = ltrim($docComment, "\r\n");

		// split comment at \n and check if a non-ignored annotation exists
		$lines = explode("\n", $docComment);
		foreach ($lines as $line) {
			if ((strpos($line, '@') === 0) && (preg_match('#^@([a-zA-Z0-9]+)(\(.+\))?#', $line, $annotations))) {
				if (isset($annotations[1]) && !in_array($annotations[1], self::$ignoredAnnotations)) {
					$attributes = array();
					// if annotations[2] is set, annotation has attributes
					if (isset($annotations[2])) {
						$attrPattern = "\'(\w+)\'\s?=\s?(\'\w+\'|\d+)";

						// at least 2 attributes
						if (preg_match('#\(((.+),(.+))+\)#', $annotations[2], $attrMatches)) {
							$attribs = explode(",", $attrMatches[1]);

							foreach ($attribs as $attrib) {
								// kill possible leading whitespaces
								$attrib = trim($attrib);

								if (preg_match('#' . $attrPattern . '#', $attrib, $attrMatch)) {
									// attrMatch[1] is identifier, attrMatch[2] is value
									$attributes[$attrMatch[1]] = $attrMatch[2];
								}
							}
						}
						// only one attribute
						else {
							if (preg_match('#\(' . $attrPattern . '\)#', $annotations[2], $attrMatch)) {
								// attrMatch[1] is identifier, attrMatch[2] is value
								$attributes[$attrMatch[1]] = $attrMatch[2];
							}
						}
					}

					// load annotation from registry and initialize possible attributes
					$annot = dsAnnotationRegistry::load($annotations[1]);
					if ($annot != NULL) {
						// initialize attributes
						$annot->initAttributes($attributes);
						$parsedAnnotations[] = $annot;
					}
				}
			}
		}

		return $parsedAnnotations;
	}
}