<?php
/*
 * This file is part of the DevelSuite
* Copyright (C) 2012 Georg Henkel <info@develman.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
namespace DevelSuite\grid\provider\propel\query;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

use \PDO as PDO;

use DevelSuite\dsApp;
use DevelSuite\grid\constants\dsColumnTypeConstants;
use DevelSuite\grid\constants\dsSortOrderConstants;
use DevelSuite\grid\filter\dsIFilter;
use DevelSuite\grid\filter\propel\dsIPropelFilter;
use DevelSuite\grid\model\propel\dsVirtualColumn;
use DevelSuite\util\dsStringTools;

/**
 * Creates a Propel Query depending on search, filter and virtual columns
 *
 * @package DevelSuite\grid\provider\propel\query
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsPropelQuery {
	/**
	 * Logger instance
	 * @var Logger
	 */
	private $log;

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
	 * The filter for the table
	 * @var dsIFilter
	 */
	private $filter;

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
	 * @param dsIFilter $filter
	 * 		Fitler for the table
	 */
	public function __construct($queryClass, array $columnModel, $filter) {
		$this->log = new Logger("PropelQuery");
		$this->log->pushHandler(new StreamHandler(LOG_PATH . DS . 'server.log'));

		$this->queryClass = $queryClass;
		$this->columnModel = $columnModel;
		$this->filter = $filter;
	}

	/**
	 * Return the offset of the query
	 */
	public function getOffset() {
		return $this->offset;
	}

	/**
	 * Returns the total count of rows in the result set
	 */
	public function getTotal() {
		return $this->total;
	}

	/**
	 * Creates the query
	 */
	public function buildQuery() {
		$this->loadRequest();
		$this->considerVirtualColumns();
		$this->considerSearch();
		$this->considerFilter();
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

	/**
	 * Build up query with user defined search
	 */
	public function considerSearch() {
		if (dsStringTools::isFilled($this->searchColumn) && dsStringTools::isFilled($this->searchQuery)) {
			$searchColumn = $this->findColumn($this->searchColumn);

			if ($searchColumn != NULL && $searchColumn->isSearchable()) {
				$extraction = $this->extractSearchQuery($searchColumn);

				// check for a virtual column
				if ($searchColumn instanceof dsVirtualColumn) {
					if (dsStringTools::isFilled($searchColumn->getJoin())) {
						if (dsStringTools::isFilled($searchColumn->getJoinType())) {
							$this->queryClass->join($searchColumn->getJoin(), $searchColumn->getJoinType());
						} else {
							$this->queryClass->join($searchColumn->getJoin());
						}
					}

					$this->queryClass->having($searchColumn->getIdentifier() . " " . $extraction["comparison"] . " ?", $extraction["query"], $extraction["type"]);
				} else {
					if (($pos = strrpos($searchColumn->getIdentifier(), ".")) !== FALSE) {
						$relation =   $this->queryClass->getModelName() . "." . substr($searchColumn->getIdentifier(), 0, $pos);
						$where = substr($searchColumn->getIdentifier(), $pos + 1);

						$count = 0;
						$parts = explode(".", $relation);
						$lastRel = NULL;
						for ($cnt = count($parts); $count < $cnt; $count++) {
							if ($count == $cnt-1) {
								$lastRel = $parts[$count];
							} else {
								$this->queryClass->join($parts[$count] . "." . $parts[$count+1]);
							}
						}

						$this->queryClass->where($lastRel . "." . $where . " " . $extraction["comparison"] . " ?", $extraction["query"], $extraction["type"]);
					} else {
						$this->log->debug("Column is a normal Column");
						$this->queryClass->filterBy($searchColumn->getIdentifier(), $extraction["query"], $extraction["comparison"]);
					}
				}

				$this->total = $this->queryClass->count();
			}
		}
	}

	/**
	 * Build up query with filters of the table
	 */
	public function considerFilter() {
		$this->log->debug("Considering filter");

		if ($this->filter != NULL && $this->filter instanceof dsIPropelFilter) {
			$this->log->debug("Building Query from filter");
			$this->filter->buildQuery($this->queryClass);

			$this->total = $this->queryClass->count();
		} else {
			$this->log->debug("Filter is NULL or it is not instance of dsIPropelFilter: " . $this->filter);
		}
	}

	public function considerVirtualColumns() {
		foreach ($this->columnModel as $column) {
			if ($column instanceof dsVirtualColumn) {
				if (dsStringTools::isFilled($column->getJoin())) {
					$this->queryClass->join($column->getJoin(), $column->getJoinType());
				}

				$this->queryClass->withColumn($column->getQuery(), $column->getIdentifier());
			}
		}
	}

	/**
	 * Retrieve the result set of the query from database
	 */
	public function query() {
		$this->log->debug("Query: " . $this->queryClass->toString());
		$resultSet = NULL;
		if ($this->limit == "Alle") {
			$resultSet = $this->queryClass->orderBy($this->sortBy, $this->sortOrder)
			->find();
		} else {
			$resultSet = $this->queryClass->orderBy($this->sortBy, $this->sortOrder)
			->offset(($this->offset - 1) * $this->limit)
			->limit($this->limit)
			->find();
		}

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

	/**
	 * Extracts the comparison type used for the searchColumn and
	 * adjusts the searchQuery.
	 *
	 * @param dsColumn $searchColumn
	 * 		The column, which is used for the search
	 */
	private function extractSearchQuery($searchColumn) {
		$extraction = array();
		$extraction["comparison"] = " = ";
		$extraction["query"] = $this->searchQuery;
		$extraction["type"] = PDO::PARAM_STR;

		if ($searchColumn->getType() === dsColumnTypeConstants::TYPE_BOOLEAN) {
			$extraction["type"] = PDO::PARAM_BOOL;
			$extraction["query"] = dsStringTools::isBoolean($this->searchQuery);
		} else if ($searchColumn->getType() === dsColumnTypeConstants::TYPE_DATE) {
			// FIXME
			// compare dates
			$extraction["query"] = $this->searchQuery;
		} else {
			$extraction["query"] = "%" . $this->searchQuery . "%";
			$extraction["comparison"] = " LIKE ";
		}

		return $extraction;
	}
}