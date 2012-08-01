<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\view\impl\flexigrid\action;

use DevelSuite\view\impl\dsFlexiGridView;

/**
 * FlexiActions can be used to add actions to the FlexiGrid table.
 * The action name is used in the event as flexi.action.<action> and
 * will be triggered if a action button in the table is pressed.
 *
 * @package DevelSuite\view\impl\flexigrid\action
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsFlexiAction implements dsIFlexiAction {
	/**
	 * Identifier for this action
	 * @var string
	 */
	private $identifier;

	/**
	 * Name of this action
	 * @var string
	 */
	protected $caption;

	/**
	 * Action, which will be called by the flexi grid table
	 * @var string
	 */
	protected $action;

	/**
	 * CSS class used for this action
	 * @var string
	 */
	protected $cssClass;

	/**
	 * Event, which will be fired
	 * @var string
	 */
	private $event;

	/**
	 * Flag, if action allows mulit selection
	 * @var bool
	 */
	protected $multiSelection = FALSE;

	/**
	 * Columns to request
	 * @var array
	 */
	protected $requestColumns = array();

	/**
	 * Constructor
	 *
	 * @param string $caption
	 * 		Caption of the action
	 * @param string $action
	 * 		Name of the action event
	 * @param string $cssClass
	 * 		Which CSS class should be used
	 */
	public function __construct($caption, $action, $cssClass = NULL) {
		$this->identifier = $action;
		$this->caption = $caption;
		$this->event = "flexi.action." . $action;
		$this->action = "flexi_do" . ucfirst($action);

		$this->cssClass = $cssClass != NULL ? $cssClass : $action;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\view\impl\flexigrid\action.dsIFlexiAction::getIdentifier()
	 */
	public function getIdentifier() {
		return $this->identifier;
	}

	/**
	 * Set multi-selection flag for this action
	 *
	 * @param bool $multiSelection
	 * 		TRUE, if action allows multi-selection
	 */
	public function setMultiSelection($multiSelection = TRUE) {
		$this->multiSelection = $multiSelection;
	}

	/**
	 * Set the columns needed for the request
	 *
	 * @param array $requestColumns
	 * 		Array with the needed columns
	 */
	public function setRequestColumns(array $requestColumns) {
		$this->requestColumns = $requestColumns;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\view\impl\flexigrid\action.dsIFlexiAction::getJSFunction()
	 */
	public function getJSFunction() {
		$code = "function " . $this->action . "(com, grid) {\n";

		// create a new jquery event: flexi.action.<eventname>
		$code .= "var event = jQuery.Event('" . $this->event . "')\n";

		// load content of the requested columns
		if (!empty($this->requestColumns)) {
			$code .= "var reqColumns = new Array();\n";
			for ($i = 0, $cnt = count($this->requestColumns); $i < $cnt; $i++) {
				$code .= "reqColumns[" . $i . "] = '" . $this->requestColumns[$i] . "';\n";
			}

			// request columns
			$code .= "\nvar resultSet = getRequestedColumns(grid, reqColumns, " . $this->multiSelection == TRUE ? "true" : "false" . ");\n\n";

			// trigger event
			$code .= "if (resultSet != null) jQuery('body').trigger(event, [resultSet]); \n";
		} else {
			// trigger event
			$code .= "jQuery('body').trigger(event);\n";
		}

		$code .= "console.log(\"Event " . $this->event . " triggered\");";
		$code .= "}\n\n";

		return $code;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\view\impl\flexigrid\action.dsIFlexiAction::__toString()
	 */
	public function __toString() {
		$code = "{ ";
		$code .= "name: '" . $this->caption . "', ";
		$code .= "bclass: '" . $this->cssClass . "', ";
		$code .= "onpress: " . $this->action . " ";
		$code .= "}";

		return $code;
	}
}