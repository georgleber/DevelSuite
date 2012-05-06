<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\serviceprovider;

/**
 * FIXME
 *
 * @package DevelSuite\serviceprovider
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
use DevelSuite\config\dsConfig;

class dsServiceProvider {
	private $parameterMap;
	private $classMap;

	public function __construct() {

	}

	public function registerParameter($alias, $value) {
		$this->parameterMap[$alias] = $value;
	}

	public function registerService($alias, $class) {
		$class = str_replace(".", "\\", $class);

		$this->classMap[$alias]["class"] = $class;
		$this->classMap[$alias]["injected"] = FALSE;
		$this->classMap[$alias]["instance"] = NULL;

	}

	public function get($alias) {
		$instance = NULL;
		if (array_key_exists($alias, $this->classMap)) {
			if ($this->classMap[$alias]["injected"] == FALSE) {
				$class = $this->classMap[$alias]["class"];

				// load class via reflection and parse methods for @Inject
				$reflClass = new \ReflectionClass($class);
				$injectionData = new dsInjectionData();
				$injectionData->parseClass($reflClass);

				
				// replace arguments against defined parameters / configured values
				$constArgs = $injectionData->getConstructorArguments();
				for ($i = 0, $cnt = count($constArgs); $i < $cnt; $i++) {
					$key = $constArgs[$i];
					if (isset($this->parameterMap[$key])) {
						$constArgs[$i] = $this->parameterMap[$key];
						continue;
					}
				}

				
				// create class
				$instance = $reflClass->newInstanceArgs($constArgs);

				// inject setter methods
				$callMethods = $injectionData->getCallMethods();
				foreach ($callMethods as $method => $arguments) {
					for ($i = 0, $cnt = count($arguments); $i < $cnt; $i++) {
						$key = $arguments[$i];
						if (isset($this->parameterMap[$key])) {
							$arguments[$i] = $this->parameterMap[$key];
							continue;
						}
					}
						
					call_user_func_array(array($instance, $method), $arguments);
				}


				$this->classMap[$alias]["instance"] = $instance;
				$this->classMap[$alias]["injected"] = TRUE;
			} else {
				$instance = $this->classMap[$alias]["instance"];
			}
		}

		return $instance;
	}

	function processPHPDoc(\ReflectionMethod $reflect)
	{

		$docComment = ltrim($docComment, "\r\n");
		$parsedDocComment = $docComment;

		$zerlegt = explode("\n", $parsedDocComment); # str_replace("\r", '', $HTTP_GET_VARS['user_input']));

		echo "parsedDocComment:";
		print_r($zerlegt);
		echo "<br/>";

		while (($newlinePos = strpos($parsedDocComment, "\n")) !== FALSE) {
			echo "NEWLINEPOS: " . $newlinePos . "<br/>";

			$parsedDocComment = substr($parsedDocComment, 0, $newlinePos);
			echo "ParsedComm: " . $parsedDocComment . "<br/>";
		}

		echo "STPOS: " . strpos($parsedDocComment, "\n") . "<br/>";

		return NULL;
		$lineNumber = 0;
		while (($newlinePos = strpos($parsedDocComment, "\n")) !== false) {
			echo "NEWLINEPOS: " . $newlinePos . "<br/>";
			$lineNumber++;
			$line = substr($parsedDocComment, 0, $newlinePos);

			$matches = array();
			if ((strpos($line, '@') === 0) && (preg_match('#^(@\w+.*?)(\n)(?:@|\r?\n|$)#s', $parsedDocComment, $matches))) {
				$tagDocblockLine = $matches[1];
				$matches2 = array();

				if (!preg_match('#^@(\w+)(\s|$)#', $tagDocblockLine, $matches2)) {
					break;
				}
				$matches3 = array();
				if (!preg_match('#^@(\w+)\s+([\w|\\\]+)(?:\s+(\$\S+))?(?:\s+(.*))?#s', $tagDocblockLine, $matches3)) {
					break;
				}
				if ($matches3[1] != 'param') {
					if (strtolower($matches3[1]) == 'return') {
						$phpDoc['return'] = array('type' => $matches3[2]);
					}
				} else {
					$phpDoc['params'][] = array('name' => $matches3[3], 'type' => $matches3[2]);
				}

				$parsedDocComment = str_replace($matches[1] . $matches[2], '', $parsedDocComment);
			}
		}
		return $phpDoc;
	}
}