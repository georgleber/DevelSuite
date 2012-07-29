<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form;

use DevelSuite\form\element\impl\dsFileInput;

use DevelSuite\form\element\impl\dsCaptcha;

use DevelSuite\form\element\dsCompositeElement;

use DevelSuite\i18n\dsResourceBundle;

use DevelSuite\form\element\impl\dsHiddenInput;

use DevelSuite\http\dsRequest;
use DevelSuite\form\element\validator\impl\dsRequiredValidator;
use DevelSuite\form\element\dsButtonNameConstants;
use DevelSuite\form\element\dsAButtonElement;
use DevelSuite\form\element\dsAElement;

/**
 * This class is the entry point for new website form.
 * It holds all elements and buttons, which are needed in
 * the form and runs the validation of the form fields.
 *
 * @package DevelSuite\form
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsForm {
	private $id;
	private $request;
	private $method = "post";
	private $action;
	private $title;
	private $enctype;
	private $isAjax;
	private $errorMessage;
	private $disabled;
	private $captchaElem;
	private $hasMandatoryElems = FALSE;

	private $showErrors = FALSE;
	private $showMandatory = TRUE;
	private $elements = array();
	private $buttons = array();

	/**
	 * Class constructor
	 *
	 * @param string $action
	 * 			Action of this form
	 * @param string $method
	 * 			The HTTP transfer method
	 */
	public function __construct($id, $request, $action, $isAjax = FALSE) {
		$this->id = $id;
		$this->request = $request;
		$this->action = $action;
		$this->isAjax = $isAjax;
	}

	/**
	 * Set method of the form
	 *
	 * @param string $method
	 * 			Method of the form
	 */
	public function setMethod($method) {
		$this->method = $method;
	}


	/**
	 * Set the title of the form
	 *
	 * @param string $title
	 * 			The title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Set the complete form disabled
	 *
	 * @param bool $disabled
	 * 			TRUE, if form should be disabled
	 */
	public function setDisabled($disabled = TRUE) {
		$this->disabled = $disabled;
	}

	/**
	 * Disables the mandatory message
	 */
	public function disableMandatoryMessage() {
		$this->showMandatory = FALSE;
	}
	
	/**
	 * Add a new form element.
	 * If the element is of type <@link DevelSuite\core\form\element\impl\dsFileInput>
	 * multipart/form-data is set into enctype of the form.
	 *
	 * @param dsAElement $element
	 * 			The new form element
	 */
	public function addElement(dsAElement $element) {
		if ($element->isMandatory()) {
			$this->hasMandatoryElems = TRUE;
			$element->addValidator(new dsRequiredValidator($element));
		}

		if ($this->disabled) {
			$element->setDisabled();
		}

		$element->setRequest($this->request);
		$this->elements[] = $element;

		if($element instanceof dsFileInput) {
			$this->enctype = "multipart/form-data";
		}
	}

	/**
	 * Add a new button element to the form
	 *
	 * @param dsAButtonElement $button
	 * 			The new button element
	 */
	public function addButton(dsAButtonElement $button) {
		$this->buttons[] = $button;
	}

	/**
	 * Checks if the form was send
	 *
	 * @return TRUE if form was send
	 */
	public function isSend() {
		$isSend = FALSE;

		if ($this->isAjax) {
			$formVal = $this->request['form'];
			if (isset($formVal) && $formVal == $this->id) {
				$isSend = TRUE;
			}
		} else {
			// check if submit was send
			if ($this->request[dsButtonNameConstants::SUBMIT]) {
				$isSend = TRUE;
			}

			// set old values of the form elements
			if ($isSend)  {
				foreach ($this->elements as $element) {
					$element->refillValues();
				}
			}
		}

		return $isSend;
	}

	/**
	 * Checks if the form was cancelled
	 *
	 * @return TRUE if form was cancelled
	 */
	public function isCancelled() {
		$isCancelled = FALSE;

		if ($this->request[dsButtonNameConstants::CANCEL]) {
			$isCancelled = TRUE;
		}

		return $isCancelled;
	}

	/**
	 * Checks if the form elements are valid.
	 *
	 * @return TRUE if form elements are valid
	 */
	public function isValid() {
		$retVal = TRUE;

		foreach ($this->elements as $element) {
			if (!$element->validate()) {
				$retVal = FALSE;
			}
		}

		return $retVal;
	}

	/**
	 * Call method to show errors
	 */
	public function showErrors() {
		$this->showErrors = TRUE;
	}

	/**
	 * @return Errors of the elements
	 */
	public function getErrors() {
		$errors = array();
		foreach ($this->elements as $element) {
			if (!$element->isValid()) {
				$errors[$element->getName()] = $element->getErrorMessage();
			}
		}

		return $errors;
	}

	/**
	 * @return The global error message, which is not set by a validator
	 */
	public function getGlobalError() {
		return $this->errorMessage;
	}

	/**
	 * Set a error message, with can not be set by a validator
	 *
	 * @param string $errorMessage
	 * 			The error message
	 */
	public function setErrorMessage($errorMessage) {
		$this->errorMessage = $errorMessage;
		$this->showErrors = TRUE;
	}

	/**
	 * Loads the element with the given element name and retrieves it value
	 *
	 * @param string $elementName
	 * 			Name of the element
	 */
	public function getValue($elementName) {
		$elem = NULL;
		foreach ($this->elements as $element) {
			if ($element->getName() == $elementName) {
				$elem = $element;
				break;
			}
		}

		if ($elem == NULL) {
			return NULL;
		}

		return $elem->getValue();
	}

	/**
	 * Add a captcha element
	 *
	 * @param string $caption
	 * 		The caption for the element
	 */
	public function showCaptcha($caption) {
		$this->captchaElem = new dsCaptcha($caption);
		$this->captchaElem->setMandatory();
		$this->addElement($this->captchaElem);
	}

	/**
	 * Clears all values of the form
	 */
	public function clear() {
		foreach ($this->elements as $element) {
			$element->setValue("");
		}
	}

	/**
	 * Depending on the request method (ajax or normal way) a array with
	 * errors is send back or the form with errors
	 *
	 * return mixed
	 * 		If it is a ajax request a array with error messages
	 * 		otherwise the error rendered form
	 */
	public function render() {
		if ($this->isAjax) {
			$opts = array();
			$opts["valid"] = TRUE;
			$opts["globalError"] = NULL;
			$opts["validationErrors"] = NULL;

			if ($this->showErrors) {
				$opts["valid"] = FALSE;

				if (isset($this->errorMessage)) {
					$opts["globalError"] = $this->errorMessage;
				} else {
					$validationErrors = array();
					foreach ($this->elements as $element) {
						if (!$element->isValid()) {
							$validationErrors[$element->getName()] = $element->getErrorMessage();
						}
					}

					$opts["validationErrors"] = $validationErrors;
				}
			}

			return $opts;
		} else {
			return $this->doRender();
		}
	}

	/**
	 * Generates the HTML of this form.
	 *
	 * @return HTML of this form
	 */
	public function doRender() {
		if (isset($this->captchaElem)) {
			$this->generateCaptchaExercise();
		}

		// generate HTML
		$html = "<form class='dsform' id ='" . $this->id . "' action='" . $this->action . "' method='" . $this->method . "'";

		// set enctype
		if (isset($this->enctype)) {
			$html .= " enctype='" . $this->enctype . "'";
		}
		$html .= ">\n";

		// set errors
		if ($this->showErrors) {
			if (isset($this->errorMessage)) {
				$html .= "<div class='dsform-errors'>\n";

				// load header text for form error message
				$bundle = dsResourceBundle::getBundle(dirname(__FILE__), "form");
				$errorText = $bundle['Form.formErrors'];
				$html .= "<p>" . $errorText . "</p>\n";

				$html .= "<ul><li>" . $this->errorMessage . "</li></ul>\n</div>\n";
			} else {
				// load header text for element error message
				$html .= "<div class='dsform-errors'>\n";

				$bundle = dsResourceBundle::getBundle(dirname(__FILE__), "form");
				$errorText = $bundle['Form.elementErrors'];
				$html .= "<p>" . $errorText . "</p>\n";

				$html .= "<ul>\n";
				foreach ($this->elements as $element) {
					if (!$element->isValid()) {
						$error = $element->getErrorMessage();
						$html .= "<li>" . $error . "</li>\n";
					}
				}
				$html .= "</ul></div>\n";
			}
		}

		if ($this->hasMandatoryElems && $this->showMandatory) {
			$html .= "<p class='mandatory'>Alle Felder mit einem <em>*</em> sind Pflichtfelder</p>";
		}
		$html .= "<fieldset class='dsform-fieldset'>\n";

		// set title
		if (isset($this->title)) {
			$html .= "<legend>" . $this->title . "</legend>\n";
		}

		// set elements
		foreach ($this->elements as $key => $element) {
			$html .= $element->getHTML();
		}

		// if this is a ajax form add a hidden input field
		if ($this->isAjax) {
			$element = new dsHiddenInput("form", $this->id);
			$html .= $element->getHTML();
		}

		// set buttons
		if(count($this->buttons) > 0) {
			$html .= "<div class='dsform-button-list'>\n";
		}
		foreach ($this->buttons as $key => $button) {
			$html .= $button->getHtml();
		}
		if(count($this->buttons) > 0) {
			$html .= "</div>\n";
		}

		$html .= "</fieldset>\n</form>\n";
		return $html;
	}

	/**
	 * Generates an exercise for the captcha element
	 */
	private function generateCaptchaExercise() {
		$captchaVal1 = rand(1, 9);
		$captchaVal2 = rand(1, 9);
		$captchaValue = md5($captchaVal1 + $captchaVal2);

		$_SESSION['captcha_val'] = $captchaValue;
		$this->captchaElem->setExercise($captchaVal1 . " + " . $captchaVal2);
	}
}