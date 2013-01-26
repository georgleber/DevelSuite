<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

use DevelSuite\dsApp;
use DevelSuite\form\button\dsAButton;
use DevelSuite\form\constants\dsButtonNameConstants;
use DevelSuite\form\element\dsAElement;
use DevelSuite\form\element\impl\dsDynamicContent;
use DevelSuite\form\element\impl\dsFileInput;
use DevelSuite\form\element\impl\dsHiddenInput;
use DevelSuite\form\validator\impl\dsRequiredValidator;
use DevelSuite\form\view\dsFormView;
use DevelSuite\i18n\dsResourceBundle;
use DevelSuite\util\dsStringTools;

/**
 * FIXME
 *
 * @package DevelSuite\form
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsForm {
	/**
	 * The responsible logger
	 * @var Logger
	 */
	protected $log;

	private $id = "dsForm";
	private $callbackUrl = NULL;
	private $action;
	private $method = 'POST';
	private $enctype = NULL;

	private $elementList = array();
	private $buttonList = array();

	private $disabled = FALSE;
	private $showMandatory = FALSE;

	private $showErrors = FALSE;
	private $errorMessage = NULL;

	public function __construct($action, $callbackUrl = NULL, $method = NULL) {
		$this->action = $action;
		$this->callbackUrl = $callbackUrl;

		if ($method != NULL) {
			$this->method = $method;
		}

		$this->log = new Logger("Form");
		$this->log->pushHandler(new StreamHandler(LOG_PATH . DS . 'server.log'));
	}

	/**
	 * Set an alternative id for the form
	 *
	 * @param string $id
	 * 		New Id for the form
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * Disbale the complete form (all elements are disabled)
	 *
	 * @param bool $disabled
	 * 			TRUE, if form should be disabled
	 */
	public function setDisabled($disabled = TRUE) {
		$this->disabled = $disabled;
	}

	/**
	 * Add a new form element.
	 * If the element is of type <@link DevelSuite\form\element\impl\dsFileInput>
	 * multipart/form-data is set into enctype of the form.
	 *
	 * @param dsAElement $element
	 * 			The new form element
	 */
	public function addElement(dsAElement $element) {
		if ($element->isMandatory()) {
			$this->showMandatory = TRUE;
			$element->addValidator(new dsRequiredValidator($element));
		}

		if ($this->disabled) {
			$element->setDisabled();
		}

		$this->elementList[] = $element;
		if($element instanceof dsFileInput) {
			$this->enctype = "multipart/form-data";
		}
	}

	/**
	 * Add a new button element to the form
	 *
	 * @param dsAButton $button
	 * 			The new button element
	 */
	public function addButton(dsAButton $button) {
		$this->buttonList[] = $button;
	}

	/**
	 * Checks if the form was send
	 *
	 * @return TRUE if form was send
	 */
	public function isSend() {
		$isSend = FALSE;

		$request = dsApp::getRequest();
		if ($request->isAjaxRequest()) {
			$formVal = $request['form'];
			if (isset($formVal) && $formVal == $this->id) {
				$isSend = TRUE;
			}
		} else {
			// check if submit was send
			if ($request[dsButtonNameConstants::SUBMIT]) {
				$isSend = TRUE;
			}

			// set old values of the form elements
			if ($isSend)  {
				foreach ($this->elementList as $element) {
					$element->populate();
				}
			}
		}

		return $isSend;
	}

	/**
	 * Checks if the form elements are valid.
	 */
	public function isValid() {
		$validResult = TRUE;

		foreach ($this->elementList as $element) {
			if (!$element->validate()) {
				$validResult = FALSE;
			}
		}

		if ($validResult === FALSE) {
			$this->showErrors = TRUE;
		}

		return $validResult;
	}

	/**
	 * Loads the element with the given element name and retrieves it value
	 *
	 * @param string $elementName
	 * 			Name of the element
	 */
	public function getValue($elementName) {
		$element = $this->findElement($elementName);

		if ($element == NULL) {
			return NULL;
		}

		return $element->getValue();
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
	 * Call method to show errors
	 */
	public function showErrors() {
		$this->showErrors = TRUE;
	}

	/**
	 * Generates the HTML of this form.
	 *
	 * @return HTML of this form
	 */
	public function render() {
		$request = dsApp::getRequest();

		// send HTML or JSON response depending on following information
		// AJAX request and form is send: send JSON
		// form is not send or request is not AJAX: send HTML
		if ($request->isAjaxRequest() && $this->isSend()) {
			$this->log->debug("Rendering response as JSON string");

			$response = array();

			// send JSON without errors
			if (!$this->showErrors) {
				$response["valid"] = TRUE;
				$response["errors"] = NULL;
			} else {
				$response["valid"] = FALSE;

				$errors = array();
				// collect global error
				if (isset($this->errorMessage)) {
					$errors["form"] = $this->errorMessage;
				}
				// collect validation error
				else {
					foreach ($this->elementList as $element) {
						if (!$element->isValid()) {
							$errors[$element->getName()] = $element->getErrorMessage();
						}
					}
				}
				$response["errors"] = $errors;
			}

			return $response;
		} else {
			$this->log->debug("Rendering response as HTML code");

			$view = new dsFormView();
			$view->assign("callbackUrl", $this->callbackUrl)
			->assign("id", $this->id)
			->assign("action", $this->action)
			->assign("method", $this->method);

			if (isset($this->enctype)) {
				$view->assign("enctype", $this->enctype);
			}

			// set errors
			if ($this->isSend() && $this->showErrors) {
				$errorMessages = array();
				if (dsStringTools::isFilled($this->errorMessage)) {
					$errorMessages[] = $this->errorMessage;
				} else {
					foreach ($this->elementList as $element) {
						if (!$element->isValid()) {
							$errorMessages[] = $element->getErrorMessage();
						}
					}
				}

				$view->assign("errorMessages", $errorMessages);
			}

			$view->assign("showMandatory", $this->showMandatory)
			->assign("elementList", $this->elementList)
			->assign("buttonList", $this->buttonList);

			$html = $view->render();
			return $html;
		}
	}

	/**
	 * Find an element by name
	 *
	 * @param string $elementName
	 * 		Name of the element
	 */
	private function findElement($elementName) {
		foreach ($this->elementList as $element) {
			if ($element->getName() == $elementName) {
				return $element;
			}
		}

		return NULL;
	}
}