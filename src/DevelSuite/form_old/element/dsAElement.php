<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form_old\element;

use DevelSuite\registry\dsRegistry;

use DevelSuite\form\element\impl\dsDynamicContent;

use DevelSuite\util\dsStringTools;
use DevelSuite\http\dsRequest;
use DevelSuite\form\element\validator\dsAValidator;
use DevelSuite\form\element\validator\dsValidatorChain;

/**
 * Abstract Superclass for all form elements.
 *
 * @package DevelSuite\form\element
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
abstract class dsAElement {
	protected $caption;
	protected $name;
	protected $disabled;

	protected $mandatory;
	protected $readOnly;
	protected $tabIndex = NULL;

	protected $cssClass = array();
	protected $appendLabel = FALSE;
	protected $request;

	private $valid = TRUE;
	private $validatorChain;
	private $errorMessage;

	/**
	 * Class constructor
	 *
	 * @param string $caption
	 * 			caption of this element
	 * @param string $name
	 * 			name of this element
	 * @param bool $mandatory
	 * 			TRUE, if this element is mandatory
	 * @param bool $readOnly
	 * 			TRUE, if this element is readOnly
	 */
	public function __construct($caption, $name) {
		$this->caption = $caption;
		$this->name = $name;
		$this->validatorChain = new dsValidatorChain();
	}

	/**
	 * Set this element mandatory
	 *
	 * @param bool $mandatory
	 * 			TRUE, if element is mandatory
	 */
	public function setMandatory($mandatory = TRUE) {
		$this->mandatory = $mandatory;
		return $this;
	}

	/**
	 * Set this element readOnly
	 *
	 * @param bool $readOnly
	 * 			TRUE, if this element should be readOnly
	 */
	public function setReadOnly($readOnly = TRUE) {
		$this->readOnly = $readOnly;
		return $this;
	}

	/**
	 * Set a tabIndex for this element
	 *
	 * @param int $tabIndex
	 * 			TabIndex for this element
	 */
	public function setTabIndex($tabIndex) {
		$this->tabIndex = $tabIndex;
		return $this;
	}

	/**
	 * Set the request object.
	 *
	 * @param dsRequest $request
	 * 			The request object.
	 */
	public function setRequest(dsRequest $request) {
		$this->request = $request;
	}

	/**
	 * Set the element disabled
	 *
	 * @param bool $disabled
	 * 			TRUE, if the element should be disabled
	 */
	public function setDisabled($disabled = TRUE) {
		$this->disabled = $disabled;
		return $this;
	}

	/**
	 * Change the default behavior of prepending labels to the element
	 * to appending labels.
	 *
	 * @param bool $appendLabel
	 * 			TRUE if labels should be appended
	 */
	public function appendLabel() {
		$this->appendLabel = TRUE;
		return $this;
	}

	/**
	 * If this element is mandatory or a value is set,
	 * the validator chain is called to run he validators.
	 *
	 * @return TRUE, if element is valid
	 */
	public function validate() {
		if ($this instanceof dsDynamicContent) {
			return TRUE;
		}

		$result = TRUE;
		$result = $this->validatorChain->processValidator();

		if (!$result) {
			$this->setInvalid();
		}

		return $result;
	}

	/**
	 * @return TRUE, if the element is valid
	 */
	public function isValid() {
		return $this->valid;
	}

	/**
	 * Set element invalid to show up error
	 */
	public function setInvalid() {
		$this->valid = FALSE;
	}

	/**
	 * Set a error message to the element.
	 *
	 * @param string $errorMessage
	 * 			The error message
	 */
	public function setErrorMessage($errorMessage) {
		$this->errorMessage = $errorMessage;
	}

	/**
	 * @return The error message of this element
	 */
	public function getErrorMessage() {
		return $this->errorMessage;
	}

	/**
	 * @return The value of this element.
	 */
	public function getValue() {
		$result = NULL;
		
		if (isset($this->request[$this->name])) {
			$result = $this->request[$this->name];
		}

		return $result;
	}

	/**
	 * @return The name of this element.
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return The caption of this element.
	 */
	public function getCaption() {
		return $this->caption;
	}

	/**
	 * @return TRUE, if this element is mandatory
	 */
	public function isMandatory() {
		return $this->mandatory;
	}

	/**
	 * Set a CSS class for this element.
	 *
	 * @param string $class
	 * 			CSS class name for this element
	 */
	public function addCssClass($class) {
		$this->cssClass[] = $class;
		return $this;
	}

	/**
	 * Add a validator to the ValidatorChain, which will be
	 * processed after submit of the form.
	 *
	 * @param dsAValidator $validator
	 * 			The new validator
	 */
	public function addValidator(dsAValidator $validator) {
		$this->validatorChain->addValidator($validator);
	}

	/**
	 * Creates a the label and appends / prepends it to the elements html
	 *
	 * @param string $html
	 * 			HTML of the element
	 * @return string $html
	 * 			HTML of the element with appended / prepended label
	 */
	protected function addLabel($html) {
		// generate label HTML
		$label = "<label for='" . $this->name . "'>" . $this->caption;

		// set mandatory
		if($this->mandatory) {
			$label .= "<em>*</em>";
		}
		$label .= "</label>\n";

		// append / prepend label
		if($this->appendLabel) {
			$html .= $label;
		} else {
			$html = $label . $html;
		}

		return $html;
	}

	/**
	 * After an error occurs reset element values to show up in form again
	 */
	public abstract function refillValues();
}