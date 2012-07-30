<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\view\impl\flexigrid\provider\propel\query;

/**
 * Creates a Propel Query to
 *
 * @package DevelSuite\view\impl\flexigrid\provider\propel\query
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
use DevelSuite\view\impl\flexigrid\constants\dsSortOrderConstants;

use DevelSuite\dsApp;

class dsPropelQuery {
	/**
	 * The Propel query class of the corresponding entity
	 * @var QueryClass
	 */
	private $queryClass;

	/**
	 * The column model
	 * @var array
	 */
	private $columnModel;

	/**
	 * offset, where to start the result set
	 * @var int
	 */
	private $offset;

	/**
	 * Limit number of results
	 * @var int
	 */
	private $limit;

	/**
	 * Column to sort the query by
	 * @var string
	 */
	private $sortBy;

	/**
	 * order of the sorting
	 * @var string
	 */
	private $sortOrder;

	/**
	 * Column of a user search
	 * @var string
	 */
	private $searchColumn;

	/**
	 * Query of a user search
	 * @var string
	 */
	private $searchQuery;
	
	/**
	 * Total count of rows in table
	 * @var int
	 */
	private $total;
	
	/**
	 * Flag, that marks the query as filtered or not
	 * @var bool
	 */
	private $filtered = FALSE;

	/**
	 * Constructor
	 *
	 * @param QueryClass $queryClass
	 * 		The query class to load data with propel
	 * @param array $columnModel
	 * 		The column model
	 */
	public function __construct($queryClass, array $columnModel) {
		$this->queryClass = $queryClass;
		$this->columnModel = $columnModel;
	}

	/**
	 * Load parameters from request for limiing / filtering the result set
	 */
	public function loadRequest() {
		$request = dsApp::getRequest();

		$this->offset = 1;
		if (isset($request['page'])) {
			$this->offset = $request['page'];
		}

		$this->total = $this->limit = $this->queryClass->count();
		if (isset($request['rp'])) {
			$this->limit = $request['rp'];
		}

		// default sort column is the ID column
		$this->sortBy = $this->findColumn("ID");
		if (isset($request['sortname'])) {
			$this->sortBy = $request['sortname'];
		}

		// default sort order is ascending
		$this->sortOrder = dsSortOrderConstants::ORDER_ASC;
		if (isset($request['sortorder'])) {
			$this->sortOrder = $request['sortorder'];
		}

		$this->searchColumn = $request['qtype'];
		$this->searchQuery = $request['query'];
	}
	
	public function query() {
		$resultSet = $this->queryClass->orderBy($this->sortBy, strtoupper($this->sortOrder))
		->offset(($page - 1) * $cnt)
		->limit($cnt)
		->find();
		
		return $resultSet;
	}

	/**
	 * Find a column by its identifier in the column model
	 *
	 * @param string $columnIdentifier
	 * 		Identifier of the column to search
	 */
	private function findColumn($columnIdentifier) {
		foreach ($this->columnModel as $column) {
			if (strtolower($column->getIdentifier()) === strtolower($columnIdentifier)) {
				return $column;
			}
		}

		return NULL;
	}
}