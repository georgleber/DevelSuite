<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\view\impl;

use DevelSuite\config\dsConfig;
use DevelSuite\exception\spl\dsFileNotFoundException;
use DevelSuite\i18n\dsResourceBundle;
use DevelSuite\template\flexigrid\dsFlexiButton;

/**
 * FIXME
 *
 * View that renders a pre-defined template with a pre-build
 * FlexiGrid table.
 *
 * @package DevelSuite\view\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsFlexiGrid extends dsHtmlView {
	private $buttons = array();
	private $requestUrl;
	private $options;
	private $title;
	private $model;
	private $columnModel = array();
	private $searchItems;
	private $singleSelect;

	public function __construct($template, $pageCtrl, $requestUrl, $model, $defaultButton = TRUE, $singleSelect = TRUE) {
		parent::__construct($template, $pageCtrl);

		$this->requestUrl = $requestUrl;
		$this->model = $model;
		$this->singleSelect = $singleSelect;

		$this->createColumnModelAndSearchItems();

		if ($defaultButton) {
			$this->initButtons();
		}
	}

	private function initButtons() {
		$button = new dsFlexiButton("add", "Erstellen");
		$this->buttons["add"] = $button;

		$button = new dsFlexiButton("edit", "Bearbeiten");
		$button->doNeedID();
		$this->buttons["edit"] = $button;

		$button = new dsFlexiButton("remove", "Löschen");
		$button->doNeedID();
		$this->buttons["remove"] = $button;

		$this->buttons["separator"] = NULL;
	}

	public function setTitle($title) {
		$this->title = $title;
	}

	public function removeButton($id) {
		if (isset($this->buttons[$id])) {
			unset($this->buttons[$id]);
		}
	}

	public function addButton($id, $name, $bclass = NULL, $onpress = NULL, $callBack = NULL, $needID = FALSE, $needMultipleIDs = FALSE) {
		$button = new dsFlexiButton($id, $name, $bclass, $onpress, $callBack);
		if ($needID) {
			$button->doNeedID();
		}

		if ($needMultipleIDs) {
			$button->doNeedMultipleIDs();
		}

		$this->buttons[$id] = $button;
	}

	public function setOptions(array $options) {
		$this->options = $options;
	}

	public function removeColumn($columnName) {
		$position = -1;
		for ($i = 0, $cntColModel = count($this->columnModel); $i < $cntColModel; $i++) {
			if ($this->columnModel[$i]["id"] == strtolower($columnName)) {
				$position = $i;
				break;
			}
		}

		if ($position != -1) {
			array_splice($this->columnModel, $position, 1);
			array_splice($this->searchItems, $position, 1);
		}
	}

	public function addColumn($display, $name, $width, $position = -1) {
		$newColumn = array("display" => $display, "name" => $name, "width" => $width, "sortable" => 'false');
		$newSearchItem = array("display" => $display, "name" => $name);
		if ($position == -1) {
			$this->columnModel[] = $newColumn;
			$this->searchItems[] = $newSearchItem;
		} else {
			array_splice($this->columnModel, $position, 0, array($newColumn));
			array_splice($this->searchItems, $position, 0, array($newSearchItem));
		}
	}

	public function getFlexiGrid() {
		$flexiGridTPL = new dsTemplate("flexigrid.tpl.php", $this->pageCtrl);
		$flexiGridTPL->setPath(dirname(__FILE__) . "/flexigrid/tpl");
		$flexiGridTPL->assign("url", $this->requestUrl);
		$flexiGridTPL->assign("colModel", $this->columnModel);
		$flexiGridTPL->assign("buttons", $this->buttons);
		$flexiGridTPL->assign("searchItems", $this->searchItems);
		$flexiGridTPL->assign("options", $this->options);
		$flexiGridTPL->assign("title", $this->title);
		$flexiGridTPL->assign("singleSelect", $this->singleSelect);

		return $flexiGridTPL->render();
	}

	public function createColumnModelAndSearchItems() {
		$columns = $this->model->getColumns();
		$table = strtolower($this->model->getPhpName());

		$useBundle = TRUE;
		try {
			$bundle = dsResourceBundle::getBundle(dsConfig::read('app.modeldir') . DS . "i18n" . DS . $table);
		} catch(dsFileNotFoundException $ex) {
			$useBundle = FALSE;
		}

		foreach ($columns as $column) {
			$colName = $column->getName();
			$colPhpName = $column->getPhpName();
			$colType = $column->getType();
			$colSize = $column->getSize();

			$modelCol = array();
			$searchItem = array();

			$modelCol["id"] = strtolower($colName);
			$searchItem["id"] = strtolower($colName);
			if ($useBundle && isset($bundle[strtolower($colName)])) {
				$modelCol["display"] = $bundle[strtolower($colName)];
				$searchItem["display"] = $bundle[strtolower($colName)];
			} else {
				$modelCol["display"] = $colPhpName;
				$searchItem["display"] = $colPhpName;
			}
			$modelCol["name"] = $colPhpName;
			$modelCol["sortable"] = 'true';
			$searchItem["name"] = $colPhpName;

			if (strtolower($colName) == "id") {
				$searchItem["isdefault"] = TRUE;
			}

			if ($colType == "INTEGER") {
				$modelCol["width"] = 30;
			} else if($colType == "BOOLEAN") {
				$modelCol["width"] = 60;
			} else {
				if ($colSize == 255) {
					$modelCol["width"] = 250;
				} else {
					$modelCol["width"] = 120;
				}
			}

			$this->columnModel[] = $modelCol;
			$this->searchItems[] = $searchItem;
		}
	}
}