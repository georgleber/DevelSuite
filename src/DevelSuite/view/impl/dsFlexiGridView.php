<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\view\impl;

use DevelSuite\util\dsStringTools;

use DevelSuite\view\impl\flexigrid\provider\dsIDataProvider;

use DevelSuite\controller\dsFrontController;

use DevelSuite\config\dsConfig;
use DevelSuite\exception\spl\dsFileNotFoundException;
use DevelSuite\i18n\dsResourceBundle;
use DevelSuite\template\flexigrid\dsFlexiButton;

/**
 * View that renders a pre-defined template with a FlexiGrid table.
 *
 * @package DevelSuite\view\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsFlexiGridView extends dsHtmlView {
	/**
	 * DataProvider is responsible for creating the column
	 * model and loading the data
	 * @var dsIDataProvider
	 */
	private $provider;

	/**
	 * Title of this table
	 * @var string
	 */
	private $title;

	/**
	 * URL to load data via JSON
	 * @var string
	 */
	private $requestUrl;

	/**
	 * Method for data sending (default is POST)
	 * @var string
	 */
	private $sendMethod = 'POST';

	/**
	 * Flag for allowing / disallowing multi-selection
	 * @var bool
	 */
	private $singleSelect = TRUE;

	/**
	 * Default ordering is ascending
	 * @var string
	 */
	private $sortOrder = "ASC";

	/**
	 * Name of the sorting column (default ID)
	 * @var string
	 */
	private $sortColumn = "ID";

	/**
	 * Height of the table (default is 300)
	 * @var int
	 */
	private $height = 300;

	/**
	 * Count of rows (default is 15)
	 * @var int
	 */
	private $rowCount = 15;

	/**
	 * Map of all actions for this FlexiGrid table
	 * @var array
	 */
	private $actionMap = array();

	/**
	 * Constructor
	 *
	 * @param string $template
	 * 		Used template
	 * @param dsFrontController $ctrl
	 * 		The corresponding controller
	 * @param string $requestUrl
	 * 		URL to load data
	 * @param string $title
	 * 		Title of the table
	 */
	public function __construct($template, dsFrontController $frontCtrl, $requestUrl, $title = NULL) {
		parent::__construct($template, $frontCtrl);

		$this->requestUrl = $requestUrl;
		$this->title = $title;
	}

	/**
	 * Set the corresponding DataProvider for handling data relevant operations
	 *
	 * @param dsIDataProvider $provider
	 * 		The corresponding DataProvider
	 */
	public function setDataProvider(dsIDataProvider $provider) {
		$this->provider = $provider;
	}

	// FIXME
	public function useDefaultActions(array $editColumns = array(), array $deleteColumns = array()) {
		$this->actionMap[] = new dsCreateAction();

		$action = new dsEditAction($editColumns);
		$action->setTable($this);
		$this->actionMap[] = $action;

		$action = new dsDeleteAction($deleteColumns);
		$action->setTable($this);
		$this->actionMap[] = $action;

		$this->actionMap[] = new dsFlexiSeparator();
	}

	// FIXME
	public function addAction(dsIFlexiAction $action) {
		$action->setTable($this);
		$this->actionMap[] = $action;
	}

	/**
	 * Set the method for sending data to the server
	 *
	 * @param string $method
	 * 		Method for sending data
	 */
	public function setSendMethod($method) {
		$this->sendMethod = $method;
	}

	/**
	 * Return the method for sending data
	 */
	public function getSendMethod() {
		return $this->sendMethod;
	}

	/**
	 * Set the sort column and the sort order
	 *
	 * @param string $sortColumn
	 * 		Identifier of the sort column
	 * @param string $sortOrder
	 * 		Sort order for the sort column
	 */
	public function setSorting($sortColumn, $sortOrder = NULL) {
		$this->sortColumn = $sortColumn;
		if (dsStringTools::isFilled($sortOrder)) {
			$this->sortOrder = $sortOrder;
		}
	}

	/**
	 * Set a row count of this table
	 *
	 * @param int $rowCount
	 * 		Count of rows to show
	 */
	public function setRowCount($rowCount) {
		$this->rowCount = $rowCount;
	}

	/**
	 * Return the row count of this table
	 */
	public function getRowCount() {
		return $this->rowCount;
	}

	/**
	 * Set the height of this table
	 *
	 * @param int $height
	 * 		Height of this table
	 */
	public function setHeight($height) {
		$this->height = $height;
	}

	/**
	 * Return the height of this table
	 */
	public function getHeight() {
		return $this->height;
	}

	/**
	 * Set this table multi-selectable
	 */
	public function setMultiSelectable() {
		$this->singleSelect = FALSE;
	}

	/**
	 * Return if this table is single-selectable
	 */
	public function isSingleSelectable() {
		return $this->singleSelect;
	}

	/**
	 * Set the title of this table
	 *
	 * @param string $title
	 * 		The title for this table
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Return title of the table (if it is not set, the entity name
	 * is taken from the dsIDataProvider)
	 */
	public function getTitle() {
		$title = $this->title;
		if (dsStringTools::isNullOrEmpty($title) && $this->provider != NULL) {
			$title = $this->provider->getEntityName();
		}

		return $title;
	}

	/**
	 * Return URL to request for loading data
	 */
	public function getRequestUrl() {
		return $this->requestUrl;
	}

	/**
	 * Return column identifier of the default sortable column
	 */
	public function getSortColumn() {
		return $this->sortColumn;
	}

	/**
	 * Return sort order of the default sortable column
	 */
	public function getSortOrder() {
		return $this->sortOrder;
	}

	/**
	 * Loads the FlexiGrid template, assigns all information to it and renders it
	 */
	public function doLayout() {
		$flexiGridView = new dsHtmlView("flexigrid.tpl.php", $this->ctrl);
		$flexiGridView->setPath(dirname(__FILE__) . DS . "flexigrid" . DS . "tpl");

		$flexiGridView->assign("title", $this->getTitle())
		->assign("url", $this->getRequestUrl())
		->assign("dataType", $this->provider->getDataType())
		->assign("method", $this->getSendMethod())
		->assign("columnModel", $this->provider->getColumnModel())
		->assign("actions", $this->actionMap) // FIXME
		->assign("sortname", $this->getSortColumn())
		->assign("sortorder", $this->getSortOrder())
		->assign("rp", $this->getRowCount())
		->assign("height", $this->getHeight())
		->assign("singleSelect", $this->isSingleSelectable());

		return $flexiGridView->render();
	}
}