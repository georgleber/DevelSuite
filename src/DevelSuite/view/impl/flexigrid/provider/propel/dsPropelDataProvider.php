<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\view\impl\flexigrid\provider\propel;

use DevelSuite\view\impl\flexigrid\provider\propel\query\dsPropelQuery;

use DevelSuite\util\dsArrayTools;

use DevelSuite\view\impl\flexigrid\renderer\dsCellRendererRegistry;

use DevelSuite\view\impl\flexigrid\provider\dsIDataProvider;

/**
 * FIXME
 *
 * @package DevelSuite\view\impl\flexigrid\provider\propel
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsPropelDataProvider implements dsIDataProvider {
	/**
	 * TableMap for analyzing the entity and creating a column model
	 * @var PropelTableMap
	 */
	private $tableMap;

	/**
	 * The column model
	 * @var array
	 */
	private $columnModel = array();

	/**
	 * Path to the i18n bundles for the corresponding table
	 * @var string
	 */
	private $bundlePath = NULL;

	/**
	 * Index of the last primary key added to the column model
	 * @var int
	 */
	private $primaryIdx = 0;

	/**
	 * The Propel query class of the corresponding entity
	 * @var QueryClass
	 */
	private $queryClass;

	/**
	 * Method for requesting data (default is POST)
	 * @var string
	 */
	private $requestMethod = 'POST';

	// FIXME
	private $virtualColumns = array();
	private $rendererRegistry;

	private $columnFilter = array();
	private $whereFilter = array();

	/**
	 * Constructor
	 *
	 * @param TableMap $tableMap
	 * 		TableMap for the entity to create the column model for
	 * @param string $bundlePath
	 * 		Path to the i18n bundle files for translation of the column names
	 */
	public function __construct($tableMap, $bundlePath = NULL) {
		$this->tableMap = $tableMap;
		$this->bundlePath = $bundlePath;

		$this->rendererRegistry = new dsCellRendererRegistry();
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\view\impl\flexigrid\provider.dsIDataProvider::setRequestMethod()
	 */
	public function setRequestMethod($requestMethod) {
		$this->requestMethod = $requestMethod;
	}

	/**
	 * Set the propel query object of the corresponding entity
	 *
	 * @param PropelQueryObject $queryObject
	 * 		PropelQueryObject for data manipulation operations
	 */
	public function setQueryClass($queryClass) {
		$this->queryClass = $queryClass;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\view\impl\flexigrid\provider.dsIDataProvider::getDataType()
	 */
	public function getDataType() {
		return "json";
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\view\impl\flexigrid\provider.dsIDataProvider::getEntityName()
	 */
	public function getEntityName() {
		return $this->tableMap->getPhpName();
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\view\impl\flexigrid\provider.dsIDataProvider::getColumnModel()
	 */
	public function getColumnModel() {
		if (empty($this->columnModel)) {
			$this->buildModel();	
		}
		
		return $this->columnModel;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\view\impl\flexigrid\provider.dsIDataProvider::addColumn()
	 */
	public function addColumn(dsColumn $column, $index = NULL) {
		// append column
		if ($index == NULL) {
			$this->columnModel[] = $column;
		} else if ($index == -1) {
			// append column behind primary keys and before rest of columns
			$this->columnModel = dsArrayTools::arrayInsert($this->columnModel, ($this->primaryIdx - 1), $column);
		} else {
			// insert column at index
			$this->columnModel = dsArrayTools::arrayInsert($this->columnModel, $index, $column);
		}
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\view\impl\flexigrid\provider.dsIDataProvider::removeColumn()
	 */
	public function removeColumn($columnIdentifier) {
		$index = $this->getColumnIndex($columnIdentifier);

		// do not allow to remove primary keys
		if ($index < $this->primaryIdx) {
			return;
		}

		$this->columnModel = dsArrayTools::arrayRemove($this->columnModel, $index);
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\view\impl\flexigrid\provider.dsIDataProvider::moveColumn()
	 */
	public function moveColumn($columnIdentifier, $targetColumnIndex) {
		$index = $this->getColumnIndex($columnIdentifier);
		$column = $this->columnModel[$index];

		$this->columnModel = dsArrayTools::arrayRemove($this->columnModel, $index);
		$this->columnModel = dsArrayTools::arrayInsert($this->columnModel, $targetColumnIndex, $column);
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\view\impl\flexigrid\provider.dsIDataProvider::getColumn()
	 */
	public function getColumn($columnIdentifier) {
		foreach ($this->columnModel as $column) {
			if (strtolower($column->getIdentifier()) === strtolower($columnIdentifier)) {
				return $column;
			}
		}

		return NULL;
	}

	public function setDefaultCellRenderer($columnType, dsICellRenderer $cellRenderer) {
		$this->rendererRegistry->setCellRenderer($columnType, $cellRenderer);
	}

	public function getDefaultCellRenderer($columnType) {
		return $this->rendererRegistry->getCellRenderer($columnType);
	}

	public function addColumnFilter(dsIColumnFilter $filter) {
		$this->columnFilter[] = $filter;
	}

	public function addWhereFilter(dsIWhereFilter $filter) {
		$this->whereFilter[] = $filter;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\view\impl\flexigrid\provider.dsIDataProvider::loadData()
	 */
	public function loadData() {
		if (empty($this->columnModel)) {
			$this->buildModel();
		}
		
		$propelQuery = new dsPropelQuery($this->queryClass, $this->columnModel);
		$propelQuery->loadRequest();
		
		$pager = NULL;
		$filtered = FALSE;

		foreach ($this->columnFilter as $filter) {
			$column = $filter->getColumn();
			$searchQry = $filter->getQuery();
			$comparisonType = $filter->getComparisonType() != NULL ? $filter->getComparisonType() : "=";

			if (strpos($column, ".") !== FALSE) {
				list($relation, $searchBy) = explode(".", $column);

				$useQueryString = "use" . $relation . "Query";
				$queryObject->{$useQueryString}()
				->filterBy($searchBy, $searchQry, $comparisonType)
				->endUse();
			} else {
				call_user_func_array(array($queryObject, 'filterBy' . $column), array($searchQry, $comparisonType));
			}

			$filtered = TRUE;
		}

		foreach ($this->whereFilter as $filter) {
			$searchQry = $filter->getQuery();
			$searchValue = $filter->getValue();
			$join = $filter->join();

			if (dsStringTools::isFilled($join)) {
				$queryObject->join($join);
			}
			$queryObject->where($searchQry, $searchValue);

			$filtered = TRUE;
		}

		$searchColumn = $this->getColumn($searchCol);
		if ($searchColumn != NULL && $searchColumn->isSearchable() && dsStringTools::isFilled($searchQuery)) {
			if (strpos($searchColumn->getIdentifier(), ".") !== FALSE) {
				list($relation, $searchBy) = explode(".", $searchColumn->getIdentifier());
				$useQueryString = "use" . $relation . "Query";
				$comparisonType = "=";
				$searchQry = "";

				if ($searchColumn->getType() === dsColumnTypes::COLUMNTYPE_BOOLEAN) {
					$searchQry = dsStringTools::is_boolean($searchQuery);
				} else if ($searchColumn->getType() === (dsColumnTypes::COLUMNTYPE_INTEGER || dsColumnTypes::COLUMNTYPE_DECIMAL)) {
					$searchQry = $searchQuery;
				} else {
					$searchQry = '%' . $searchQuery . '%';
					$comparisonType = 'LIKE';
				}

				$queryObject->{$useQueryString}()
				->filterBy($searchBy, $searchQry, $comparisonType)
				->endUse();
			} else {
				$column = $this->tableMap->getColumn($searchColumn->getIdentifier());
				if ($column->getType() == "BOOLEAN" && dsStringTools::is_boolean($searchQuery)) {
					$queryObject->filterBy($column->getPhpName(), dsStringTools::is_boolean($searchQuery));
				} else if ($column->isNumeric() && is_numeric($searchQuery)) {
					$queryObject->filterBy($column->getPhpName(), $searchQuery);
				} else {
					$searchQuery = '%' . $searchQuery . '%';
					$queryObject->filterBy($column->getPhpName(), $searchQuery, " LIKE ");
				}
			}
			$filtered = TRUE;
		}

		foreach ($this->virtualColumns as $virtualColumn) {
			if (dsStringTools::isFilled($virtualColumn->getJoin())) {
				$queryObject->join($virtualColumn->getJoin(), $virtualColumn->getJoinType());
			}

			$queryObject->withColumn($virtualColumn->getQuery(), $virtualColumn->getIdentifier());
		}

		$retVal = array();
		$retVal["page"] = $page;

		if ($filtered) {
			$retVal["total"] = $queryObject->count();
		} else {
			$retVal["total"] = $total;
		}

		$resultSet = $propelQuery->query();
		
		$rows = array();
		$rowCnt = 1;
		foreach ($resultSet as $result) {
			$cells = array();
			$objectArr = $result->toArray("phpName", TRUE, array(), TRUE);
			foreach ($this->columnModel as $column) {
				$renderer = NULL;
				if ($column->getCellRenderer() !== NULL) {
					$renderer = $column->getCellRenderer();
				} else {
					$renderer = $this->rendererRegistry->getCellRenderer($column->getType());
					$renderer->setColumn($column);
				}

				if ($column->isVirtual()) {
					$method = "get" . $column->getIdentifier();
					$virtualResult = NULL;
					if (is_callable(array($result, $method))) {
						$virtualResult = call_user_func(array($result, $method));
					}

					if ($virtualResult != NULL) {
						$renderer->setValue($virtualResult);
					}
				} else {
					$relation = $result;
					$columnIdent = $column->getIdentifier();
					while (($pos = strpos($columnIdent, ".")) !== FALSE) {
						$tableName = substr($columnIdent, 0, $pos);
						$columnName = substr($columnIdent, $pos + 1);

						$method = "get" . $tableName;
						if (method_exists($relation, $method) && is_callable(array($relation, $method))) {
							$relation = call_user_func(array($relation, $method));
						}

						$columnIdent = $columnName;
					}

					if ($relation != NULL) {
						$renderer->setValue($relation->getByName($columnIdent));
					} else {
						if (array_key_exists($column->getIdentifier(), $objectArr)) {
							$renderer->setValue($result->getByName($column->getIdentifier()));
						}
					}
				}

				$cells[$column->getIdentifier()] = $renderer->render();
			}

			$rows[] = array('id' => $rowCnt, 'cell' => $cells);
			$rowCnt;
		}

		$retVal["rows"] = $rows;
		return $retVal;
	}

	private function buildModel() {
		$bundle = NULL;
		if (dsStringTools::isFilled($this->bundlePath)) {
			$table = strtolower($this->tableMap->getPhpName());
			try {
				$bundle = dsResourceBundle::getBundle($this->bundlePath . DS . $table);
			} catch(dsFileNotFoundException $ex) {
				// do nothing
			}
		}

		// first add primary keys
		$primaryKeys = $this->tableMap->getPrimaryKeys();
		foreach ($primaryKeys as $primaryKeyColumn) {
			$caption = $identifier = $primaryKeyColumn->getPhpName();
			$columnType = $primaryKeyColumn->getType();
			$columnSize = $primaryKeyColumn->getSize();

			if ($bundle !== NULL && isset($bundle[strtolower($identifier)])) {
				$caption = $bundle[strtolower($identifier)];
			}

			$column = new dsColumn($caption, $identifier, $columnType, $columnSize);

			if ($primaryKeyColumn->isForeignKey()) {
				$relatedTable = $primaryKeyColumn->getRelation()->getName();
				$column->setRelatedTable($relatedTable);

				$column->setSearchable(FALSE);
				$column->setWidth(120);
			} else {
				$column->setHide(TRUE);
				$column->setSearchable(TRUE);
			}

			$this->columnModel[] = $column;
			$this->primaryIdx++;
		}

		// add rest of the columns
		$firstColumn = TRUE;
		$columns = $this->tableMap->getColumns();
		foreach ($columns as $column) {
			// do not add lob (BLOB, LONGVARBINARY, VARBINARY) columns
			// prevent adding of primary keys again
			if ($column->isLob() || $column->isPrimaryKey()) {
				continue;
			}

			$caption = $identifier = $column->getPhpName();
			$columnType = $column->getType();
			$columnSize = $column->getSize();

			if ($bundle !== NULL && isset($bundle[strtolower($identifier)])) {
				$caption = $bundle[strtolower($identifier)];
			}

			$modelCol = new dsColumn($caption, $identifier, $columnType, $columnSize);
			$modelCol->setSearchable(TRUE);

			if ($firstColumn) {
				$modelCol->setDefaultSearchColumn(TRUE);
			}

			if ($column->isForeignKey()) {
				$relatedTable = $column->getRelation()->getForeignTable()->getPhpName();
				$modelCol->setRelatedTable($relatedTable);
			}

			$this->columnModel[] = $modelCol;
			$firstColumn = FALSE;
		}
	}

	private function getColumnIndex($columnIdentifier) {
		for ($i = 0, $cnt = count($this->columnModel); $i < $cnt; $i++) {
			$ident = $this->columnModel[$i]->getIdentifier();
			if (strtolower($ident) === strtolower($columnIdentifier)) {
				return $i;
			}
		}
	}
}