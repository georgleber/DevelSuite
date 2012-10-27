<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\grid\renderer;

use DevelSuite\grid\constants\dsColumnTypeConstants;
use DevelSuite\grid\renderer\impl\dsBooleanCellRenderer;
use DevelSuite\grid\renderer\impl\dsDateCellRenderer;
use DevelSuite\grid\renderer\impl\dsDecimalCellRenderer;
use DevelSuite\grid\renderer\impl\dsDefaultCellRenderer;
use DevelSuite\grid\renderer\impl\dsIntegerCellRenderer;
use DevelSuite\grid\renderer\impl\dsStringCellRenderer;

use \PDO as PDO;

/**
 * Registry for setup predefined CellRenderer for the column types
 *
 * @package DevelSuite\grid\renderer
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsCellRendererRegistry {
	/**
	 * Map with all CellRenderer mapped to a column type
	 * @var array
	 */
	private $rendererMap = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->rendererMap[dsColumnTypeConstants::TYPE_STRING] = new dsStringCellRenderer();
		$this->rendererMap[dsColumnTypeConstants::TYPE_TEXT] = new dsStringCellRenderer();
		$this->rendererMap[dsColumnTypeConstants::TYPE_INTEGER] = new dsIntegerCellRenderer();
		$this->rendererMap[dsColumnTypeConstants::TYPE_DECIMAL] = new dsDecimalCellRenderer();
		$this->rendererMap[dsColumnTypeConstants::TYPE_DATE] = new dsDateCellRenderer();
		$this->rendererMap[dsColumnTypeConstants::TYPE_BOOLEAN] = new dsBooleanCellRenderer();
	}

	/**
	 * Register an other CellRenderer for a column type
	 *
	 * @param string $columnType
	 * 		The column type to change the CellRenderer for
	 * @param dsICellRenderer $renderer
	 * 		The CellRenderer to use for the column type
	 */
	public function setCellRenderer($columnType, dsICellRenderer $renderer) {
		$this->rendererMap[$columnType]	= $renderer;
	}

	/**
	 * Return the CellRenderer for a column type
	 *
	 * @param string $columnType
	 * 		The column type, for which the CellRenderer will be returned
	 */
	public function getCellRenderer($columnType) {
		if (isset($this->rendererMap[$columnType])) {
			return $this->rendererMap[$columnType];
		} else {
			return new dsDefaultCellRenderer();
		}
	}
}