<?php
namespace DevelSuite\serviceprovider;

class dsInjectionData {
	private $constructorArguments = array();
	private $callMethods = array();

	public function parseClass(\ReflectionClass $reflectionClass) {
		$methods = $reflectionClass->getMethods();

		foreach ($methods as $method) {
			$docComment = $method->getDocComment();

			// no comment exists
			if (trim($docComment) == '') {
				return NULL;
			}

			$docComment = preg_replace('#[ \t]*(?:\/\*\*|\*\/|\*)?[ ]{0,1}(.*)?#', '$1', $docComment);
			$docComment = ltrim($docComment, "\r\n");

			$arguments = array();
			// split comment at \n and check if @Inject exists
			$lines = explode("\n", $docComment);
			foreach ($lines as $line) {
				if ((strpos($line, '@') === 0) && (preg_match('#^@Inject\s(.+)#s', $line, $matches))) {
					$arguments[] = $matches[1];
				}
			}

			if ($method->isConstructor()) {
				foreach ($arguments as $arg) {
					$this->constructorArguments[] = $arg;
				}
			} else {
				$this->callMethods[$method->getName()] = $arguments;
			}
		}
	}

	public function getConstructorArguments() {
		return $this->constructorArguments;
	}

	public function getCallMethods() {
		return $this->callMethods;
	}
}