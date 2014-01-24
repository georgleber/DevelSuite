<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\grid\provider;

use DevelSuite\grid\filter\dsIColumnFilter;
use DevelSuite\grid\filter\dsIFilter;
use DevelSuite\grid\filter\dsIWhereFilter;
use DevelSuite\grid\model\dsColumn;
use DevelSuite\grid\renderer\dsICellRenderer;

/**
 * Interface for all DataProvider
 *
 * @package DevelSuite\grid\provider
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
interface dsIDataProvider {
	/**
	 * The type of data which the DataProvider provides
	 */
	public function getDataType();

	/**
	 * Name of the entity
	 */
	public function getEntityName();

	/**
	 * Provide a column model which is used in the GUI
	 */
	public function getColumnModel();
        
	/**
	 * Load / reload the data
	 */
	public function loadData();

	/**
	 * Add a filter for the table
	 *
	 * @param dsIFilter $filter
	 * 		Any applicable filter for the table
	 */
	public function addFilter(dsIFilter $filter);

	/**
	 * Add a column to the model
	 *
	 * @param dsColumn $column
	 * 		The column to add
	 * @param int $index
	 * 		Position, where the column should be added
	 */
	public function addColumn(dsColumn $column, $index = NULL);

	/**
	 * Remove a column from the model
	 *
	 * @param string $columnIdentifier
	 * 		Identifier of the column
	 */
	public function removeColumn($columnIdentifier);

	/**
	 * Move a column to another position
	 *
	 * @param sting $columnIdentifier
	 * 		Identifier of the column
	 * @param int $targetColumnIndex
	 * 		Position, where the column should be moved to
	 */
	public function moveColumn($columnIdentifier, $targetColumnIndex);

	/**
	 * Retrieve a column from the model
	 *
	 * @param string $columnIdentifier
	 * 		Identifier of the column
	 */
	public function getColumn($columnIdentifier);

	/**
	 * Set a default CellRenderer for a column type
	 *
	 * @param string $columnType
	 * 		The column type to set the renderer for
	 * @param dsICellRenderer $cellRenderer
	 * 		The new CellRenderer
	 */
	public function setDefaultCellRenderer($columnType, dsICellRenderer $cellRenderer);

	/**
	 * Return the CellRenderer for a column type
	 *
	 * @param string $columnType
	 * 		The column type, for which the CellRenderer will be returned
	 */
	public function getDefaultCellRenderer($columnType);
}