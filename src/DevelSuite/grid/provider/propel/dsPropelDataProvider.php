<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\grid\provider\propel;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

use DevelSuite\exception\spl\dsFileNotFoundException;
use DevelSuite\grid\constants\dsColumnTypeConstants;
use DevelSuite\grid\filter\dsIColumnFilter;
use DevelSuite\grid\filter\dsIFilter;
use DevelSuite\grid\filter\dsIWhereFilter;
use DevelSuite\grid\model\dsColumn;
use DevelSuite\grid\model\propel\dsPropelColumn;
use DevelSuite\grid\model\propel\dsVirtualColumn;
use DevelSuite\grid\provider\dsIDataProvider;
use DevelSuite\grid\provider\propel\query\dsPropelQuery;
use DevelSuite\grid\renderer\dsICellRenderer;
use DevelSuite\grid\renderer\dsCellRendererRegistry;
use DevelSuite\i18n\dsResourceBundle;
use DevelSuite\util\dsArrayTools;
use DevelSuite\util\dsStringTools;

/**
 * PropelDataProvider uses Propel for building the column model and 
 * loading data from database.
 *
 * @package DevelSuite\grid\provider\propel
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsPropelDataProvider implements dsIDataProvider {
	/**
	 * The responsible logger
	 * @var Logger
	 */
	private $log;

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

	/**
	 * Additional filter for the table 
	 * @var dsIFilter
	 */
	private $filter;

	/**
	 * Constructor
	 *
	 * @param TableMap $tableMap
	 * 		TableMap for the entity to create the column model for
	 * @param string $bundlePath
	 * 		Path to the i18n bundle files for translation of the column names
	 */
	public function __construct($tableMap, $bundlePath = NULL) {
		$this->log = new Logger("PropelDataProvider");
		$this->log->pushHandler(new StreamHandler(LOG_PATH . DS . 'server.log'));

		$this->tableMap = $tableMap;
		$this->bundlePath = $bundlePath;

		$this->rendererRegistry = new dsCellRendererRegistry();
		$this->buildModel();
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
	 * @see DevelSuite\grid\provider.dsIDataProvider::getDataType()
	 */
	public function getDataType() {
		return "json";
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\grid\provider.dsIDataProvider::getEntityName()
	 */
	public function getEntityName() {
		return $this->tableMap->getPhpName();
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\grid\provider.dsIDataProvider::getColumnModel()
	 */
	public function getColumnModel() {
		return $this->columnModel;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\grid\provider.dsIDataProvider::addColumn()
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
	 * @see DevelSuite\grid\provider.dsIDataProvider::removeColumn()
	 */
	public function removeColumn($columnIdentifier) {
		$index = $this->getColumnIndex($columnIdentifier);

		$this->log->debug("Removig column " . $columnIdentifier .", found on index: " . $index . ", current primaryIdx: " . $this->primaryIdx);

		// do not allow to remove primary keys
		if ($index < $this->primaryIdx) {
			return;
		}

		$this->columnModel = dsArrayTools::arrayRemove($this->columnModel, $index);
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\grid\provider.dsIDataProvider::moveColumn()
	 */
	public function moveColumn($columnIdentifier, $targetColumnIndex) {
		$index = $this->getColumnIndex($columnIdentifier);
		$column = $this->columnModel[$index];

		$this->columnModel = dsArrayTools::arrayRemove($this->columnModel, $index);
		$this->columnModel = dsArrayTools::arrayInsert($this->columnModel, $targetColumnIndex, $column);
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\grid\provider.dsIDataProvider::getColumn()
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
	 * @see DevelSuite\grid\provider.dsIDataProvider::setDefaultCellRenderer()
	 */
	public function setDefaultCellRenderer($columnType, dsICellRenderer $cellRenderer) {
		$this->rendererRegistry->setCellRenderer($columnType, $cellRenderer);
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\grid\provider.dsIDataProvider::getDefaultCellRenderer()
	 */
	public function getDefaultCellRenderer($columnType) {
		return $this->rendererRegistry->getCellRenderer($columnType);
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\grid\provider.dsIDataProvider::addFilter()
	 */
	public function addFilter(dsIFilter $filter) {
		$this->filter = $filter;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\grid\provider.dsIDataProvider::loadData()
	 */
	public function loadData() {
		$propelQuery = new dsPropelQuery($this->queryClass, $this->columnModel, $this->filter);
		$propelQuery->buildQuery();

		// retrieve ResultSet from PropelQuery
		$resultSet = $propelQuery->query();

		$retVal = array();
		$retVal["page"] = $propelQuery->getOffset();
		$retVal["total"] = $propelQuery->getTotal();

		$rowCnt = 1;
		$rows = array();
		foreach ($resultSet as $result) {
			print_r($result);
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

					if ($relation != NULL && $relation !== $result) {
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
	 * Build the column model of the specified propel entity
	 */
	private function buildModel() {
		$bundle = NULL;
		if (dsStringTools::isFilled($this->bundlePath)) {
			$table = strtolower($this->tableMap->getPhpName());
			try {
				$bundle = dsResourceBundle::getBundle($this->bundlePath, $table);
			} catch(dsFileNotFoundException $ex) {
				// do nothing
			}
		}

		// first add primary keys
		$primaryKeys = $this->tableMap->getPrimaryKeys();
		foreach ($primaryKeys as $primaryKeyColumn) {
			$caption = $identifier = $primaryKeyColumn->getPhpName();
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

			$caption = $identifier = $column->getPhpName();
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

	/**
	 * Search for the column in model and return the index of it
	 *
	 * @param string $columnIdentifier
	 * 		The identifier of the column
	 */
	private function getColumnIndex($columnIdentifier) {
		for ($i = 0, $cnt = count($this->columnModel); $i < $cnt; $i++) {
			$ident = $this->columnModel[$i]->getIdentifier();
			if (strtolower($ident) === strtolower($columnIdentifier)) {
				return $i;
			}
		}
	}
}