<?php
namespace DevelSuite\form\annotation;

use DevelSuite\reflection\annotations\annotation\dsAnnotation;

class Length extends dsAnnotation {
	private $min;
	private $max;

	public function getMin() {
		return $this->min;
	}

	public function getMax() {
		return $this->max;
	}

	public function initAttributes(array $attributes) {
		// at least one property must be set
		$needOne = FALSE;

		if (array_key_exists('min', $attributes)) {
			$this->min = $attributes['min'];
			$needOne = TRUE;
		}

		if (array_key_exists('max', $attributes)) {
			$this->max = $attributes['max'];
			$needOne = TRUE;
		}

		if (!$needOne) {
			throw new \Exception("LengthAnnotation needs at least min or max value!");
		}
	}
}