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
	 * Corresponging FlexiGridTable
	 * @var dsFlexiGridTable
	 */
	protected $table;

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
	 * Columns to request
	 * @var array
	 */
	private $requestColumns = array();

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
		$this->caption = $caption;
		$this->event = "flexi.action." . $action;
		$this->action = "flexi_do" . ucfirst($action);

		$this->cssClass = $cssClass != NULL ? $cssClass : $action;
	}

	/**
	 * Set the corresponding FlexgiGridTable
	 *
	 * @param dsFlexiGridTable $table
	 */
	public function setTable(dsFlexiGridView $table) {
		$this->table = $table;
	}

	/**
	 * Set the columns, which should be loaded by this action
	 *
	 * @param array $columns
	 * 		The colums, which content will be requested
	 */
	public function setRequestColumns(array $columns) {
		$this->requestColumns = $columns;
	}

	/**
	 * Creates code for a javascript function depending on this action and its needs.
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

			if ($this->table != NULL && $this->table->isSingleSelect()) {
				$code .= "\nvar reqContents = getRequestedContent(grid, reqColumns, true);\n\n";
			} else {
				$code .= "\nvar reqContents = getRequestedContent(grid, reqColumns, false);\n\n";
			}

			// trigger event
			$code .= "if (reqContents != null) {\n";
			if ($this->table != NULL && $this->table->isSingleSelect()) {
				$code .= "jQuery('body').trigger(event, [reqContents, true]);\n";
			} else {
				$code .= "jQuery('body').trigger(event, [reqContents, false]);\n";
			}
			$code .= "console.log(\"Event " . $this->event . " triggered\");";
			$code .= "}\n";
		} else {
			// trigger event
			$code .= "jQuery('body').trigger(event);\n";
		}
		$code .= "}\n\n";

		return $code;
	}

	/**
	 * (non-PHPdoc)
	 * @see DevelSuite\core\template\flexigrid2.dsIFlexiSeparator::__toString()
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