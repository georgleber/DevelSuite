<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form;

use DevelSuite\form\button\dsAButton;

use DevelSuite\form\validator\impl\dsRequiredValidator;

use DevelSuite\form\element\dsAElement;

use DevelSuite\form\element\impl\dsHiddenInput;

use DevelSuite\form\element\impl\dsFieldset;

use DevelSuite\util\dsStringTools;

use DevelSuite\form\constants\dsButtonNameConstants;

use DevelSuite\dsApp;

/**
 * FIXME
 *
 * @package DevelSuite\form
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsForm {
	private $id = "dsForm";
	private $action;
	private $method = 'POST';
	private $enctype = NULL;

	private $elementList = array();
	private $buttonList = array();

	private $disabled = FALSE;
	private $showMandatory = FALSE;
	private $containsFieldsets = FALSE;

	public function __construct($action, $method = NULL) {
		$this->action = $action;

		if ($method != NULL) {
			$this->method = $method;
		}
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

			if (!($element instanceof dsFieldset)) {
				$element->addValidator(new dsRequiredValidator($element));
			}
		}

		$this->elementList[] = $element;
		if ($element instanceof dsFieldset) {
			$this->containsFieldsets = TRUE;
			if ($element->containsFileInput()) {
				$this->enctype = "multipart/form-data";
			}
		} else {
			if ($this->disabled) {
				$element->setDisabled();
			}

			if($element instanceof dsFileInput) {
				$this->enctype = "multipart/form-data";
			}
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
				foreach ($this->elements as $element) {
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
		$retVal = TRUE;

		foreach ($this->elements as $element) {
			if (!$element->validate()) {
				$retVal = FALSE;
			}
		}

		return $retVal;
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

		return $elem->getValue();
	}

	/**
	 * Clears all values of the form
	 */
	public function clear() {
		foreach ($this->elements as $element) {
			if ($element instanceof dsFieldset) {
				$element->clear();
			} else {
				$element->setValue(NULL);
			}
		}
	}

	/**
	 * Generates the HTML of this form.
	 *
	 * @return HTML of this form
	 */
	public function render() {
		// generate HTML
		$html = "<form class='dsform' id ='" . $this->id . "' action='" . $this->action . "' method='" . $this->method . "'";

		// set enctype
		if (isset($this->enctype)) {
			$html .= " enctype='" . $this->enctype . "'";
		}

		$html .= ">\n";

		if ($this->showMandatory) {
			$html .= "<p class='dsform-mandatory'>Alle Felder mit einem <em>*</em> sind Pflichtfelder</p>";
		}

		// add a hidden input field
		$element = new dsHiddenInput("form", $this->id);
		$html .= $element->buildHTML();

		if ($this->containsFieldsets) {
			// add elements
			foreach ($this->elementList as $key => $element) {
				$html .= $element->buildHTML();
			}
		} else {
			$html .= "<fieldset>\n";
			$html .= "<ul>\n";
				
			// add elements
			foreach ($this->elementList as $key => $element) {
				$html .= "<li class='dsform-formRow'>\n";
				$html .= $element->buildHTML();
				$html .= "</li>\n";
			}
				
			$html .= "</ul>\n";
			$html .= "</fieldset>\n";
		}

		// add buttons
		if(count($this->buttons) > 0) {
			$html .= "<div class='dsform-buttons'>\n";

			foreach ($this->buttonList as $key => $button) {
				$html .= $button->getHtml();
			}

			$html .= "</div>\n";
		}

		$html .= "</form>\n";
		return $html;
	}

	/**
	 * Find an element by name
	 *
	 * @param string $elementName
	 * 		Name of the element
	 */
	private function findElement($elementName) {
		foreach ($this->elementList as $element) {
			if ($element instanceof dsFieldset) {
				$elem = $element->findElement($elementName);
				if ($elem != NULL) {
					return $elem;
				}
			} else {
				if ($element->getName() == $elementName) {
					return $element;
				}
			}
		}

		return NULL;
	}
}