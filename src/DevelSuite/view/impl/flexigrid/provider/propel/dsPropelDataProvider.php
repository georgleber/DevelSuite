<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\view\impl\flexigrid\provider\propel;

use DevelSuite\exception\spl\dsFileNotFoundException;
use DevelSuite\i18n\dsResourceBundle;
use DevelSuite\util\dsArrayTools;
use DevelSuite\view\impl\flexigrid\constants\dsColumnTypeConstants;
use DevelSuite\view\impl\flexigrid\model\dsColumn;
use DevelSuite\view\impl\flexigrid\model\propel\dsPropelColumn;
use DevelSuite\view\impl\flexigrid\model\propel\dsVirtualColumn;
use DevelSuite\view\impl\flexigrid\provider\dsIDataProvider;
use DevelSuite\view\impl\flexigrid\provider\propel\query\dsPropelQuery;
use DevelSuite\view\impl\flexigrid\renderer\dsCellRendererRegistry;

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
	 * Registry with all defined dSICellRenderer
	 * @var array
	 */
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

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\view\impl\flexigrid\provider.dsIDataProvider::setDefaultCellRenderer()
	 */
	public function setDefaultCellRenderer($columnType, dsICellRenderer $cellRenderer) {
		$this->rendererRegistry->setCellRenderer($columnType, $cellRenderer);
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\view\impl\flexigrid\provider.dsIDataProvider::getDefaultCellRenderer()
	 */
	public function getDefaultCellRenderer($columnType) {
		return $this->rendererRegistry->getCellRenderer($columnType);
	}

	// FIXME
	public function addColumnFilter(dsIColumnFilter $filter) {
		$this->columnFilter[] = $filter;
	}

	// FIXME
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
		$propelQuery->buildQuery();

		/*
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
			*/

		// retrieve ResultSet from PropelQuery
		$resultSet = $propelQuery->query();

		$retVal = array();
		$retVal["page"] = $propelQuery->getOffset();
		$retVal["total"] = count($resultSet);

		$rowCnt = 1;
		$rows = array();
		foreach ($resultSet as $result) {
			$cells = array();
			$objectArr = $result->toArray("phpName", TRUE, array(), TRUE);

			foreach ($this->columnModel as $column) {

				// load column specific CellRenderer if it is set,
				// otherwise load it from the registry
				$cellRenderer = NULL;
				if ($column->getCellRenderer() !== NULL) {
					$cellRenderer = $column->getCellRenderer();
				} else {
					$cellRenderer = $this->rendererRegistry->getCellRenderer($column->getType());
					$cellRenderer->setColumn($column);
				}


				if ($column instanceof dsVirtualColumn) {
					$method = "get" . $column->getIdentifier();
						
					$virtualResult = NULL;
					if (is_callable(array($result, $method))) {
						$virtualResult = call_user_func(array($result, $method));
					}

					if ($virtualResult != NULL) {
						$cellRenderer->setValue($virtualResult);
					}
				} else {
					$relation = $result;
					$columnIdent = $column->getIdentifier();
						
					// if column contains .'s, it is a relation column
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
						$cellRenderer->setValue($relation->getByName($columnIdent));
					} else {
						if (array_key_exists($column->getIdentifier(), $objectArr)) {
							$cellRenderer->setValue($result->getByName($column->getIdentifier()));
						}
					}
				}

				$cells[$column->getIdentifier()] = $cellRenderer->render();
			}

			$rows[] = array('id' => $rowCnt, 'cell' => $cells);
			$rowCnt++;
		}

		$retVal["rows"] = $rows;
		return $retVal;
	}

	/**
	 *
	 */
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
			$identifier = $primaryKeyColumn->getName();
			$caption = $primaryKeyColumn->getPhpName();
			$columnType = dsPropelTypeMapper::mapPropelType($primaryKeyColumn->getType());

			if ($bundle !== NULL && isset($bundle[strtolower($identifier)])) {
				$caption = $bundle[strtolower($identifier)];
			}

			// create column
			$column = new dsPropelColumn($identifier, $columnType, $caption);
			if ($primaryKeyColumn->isForeignKey()) {
				$relatedTable = $primaryKeyColumn->getRelation()->getName();
				$column->setRelatedTable($relatedTable);
			} else {
				$column->setHide();
				$column->setSearchable();
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
			
			$identifier = $column->getName();
			$caption = $column->getPhpName();
			$columnType = dsPropelTypeMapper::mapPropelType($column->getType());

			if ($bundle !== NULL && isset($bundle[strtolower($identifier)])) {
				$caption = $bundle[strtolower($identifier)];
			}

			$modelCol = new dsPropelColumn($identifier, $columnType, $caption);
			if ($firstColumn) {
				$modelCol->setDefaultSearchColumn();
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