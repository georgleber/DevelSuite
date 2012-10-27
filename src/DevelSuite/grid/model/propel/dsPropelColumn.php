<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\grid\model\propel;

use DevelSuite\grid\model\dsColumn;

/**
 * Column to work with in the FlexiGridTable and in the dsPropelDataProvider
 *
 * @package DevelSuite\grid\model\propel
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsPropelColumn extends dsColumn {
	/**
	 * Flag, if this column is a pk column
	 * @var bool
	 */
	private $primaryKey = FALSE;

	/**
	 * The name of a related table
	 * @var string
	 */
	private $relatedTable = NULL;
	
	/**
	 * Constructor
	 *
	 * @param string $identifier
	 * 		Identifier of this column
	 * @param int $type
	 * 		PDO type of this column
	 * @param string $caption
	 * 		Caption of this column (if not set, identifier is used)
	 */
	public function __construct($identifier, $type, $caption = NULL) {
		parent::__construct($identifier, $type, $caption);
	}

	/**
	 * Sets this column as primary key column
	 *
	 * @param bool $primaryKey
	 * 		TRUE, if this colum is a primary key column
	 */
	public function setPrimaryKey($primaryKey = TRUE) {
		$this->primaryKey = $primaryKey;
	}

	/**
	 * Return if column is a primary key column
	 */
	public function isPrimaryKey() {
		return $this->primaryKey;
	}

	/**
	 * Set the name of the related table
	 *
	 * @param string $relatedTable
	 *		Name of the related table
	 */
	public function setRelatedTable($relatedTable) {
		$this->relatedTable = $relatedTable;
	}

	/**
	 * Return the name of the related table
	 */
	public function getRelatedTable() {
		return $this->relatedTable;
	}
}