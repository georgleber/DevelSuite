<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\view\impl\flexigrid\model;

use DevelSuite\view\impl\flexigrid\constants\dsAlignmentConstants;
use DevelSuite\util\dsStringTools;

use \PDO as PDO;

/**
 * Column to work with in the FlexiGridTable and in the dsIDataProvider
 *
 * @package DevelSuite\view\impl\flexigrid\model
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsColumn {
	/**
	 * Identifier for this column
	 * @var string
	 */
	private $identifier;

	/**
	 * Visible caption in the table
	 * @var string
	 */
	private $caption;

	/**
	 * PDO type of the column
	 * @var int
	 */
	private $type;

	/**
	 * Width of the column in the table
	 * @var int
	 */
	private $width = 0;

	/**
	 * Alignment of the column in the table
	 * @var int
	 */
	private $alignment;

	/**
	 * Flag, if this column is hidden in the table
	 * @var bool
	 */
	private $hide = FALSE;

	/**
	 * Flag, if this column is searchable
	 * @var bool
	 */
	private $searchable = FALSE;

	/**
	 * Flagm, if this column is sortable
	 * @var unknown_type
	 */
	private $sortable = FALSE;

	/**
	 * Flag, if this column is the default search column
	 * @var bool
	 */
	private $defaultSearchColumn = FALSE;

	/**
	 * A dsICellRenderer for this column
	 * @var dsICellRenderer
	 */
	private $cellRenderer;

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
		$this->identifier = $identifier;

		if (dsStringTools::isFilled($caption)) {
			$this->caption = $caption;
		} else {
			$this->caption = $identifier;
		}

		$this->type = $type;
	}

	/**
	 * Return the identifier of this column
	 */
	public function getIdentifier() {
		return $this->identifier;
	}

	/**
	 * Set a caption for this column
	 *
	 * @param string $caption
	 * 		The caption of this column
	 */
	public function setCaption($caption) {
		$this->caption = $caption;
	}

	/**
	 * Return the caption of this column
	 */
	public function getCaption() {
		return $this->caption;
	}

	/**
	 * Return the PDO type of this column
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Set the width of this column
	 *
	 * @param int $width
	 * 		Width of this column
	 */
	public function setWidth($width) {
		$this->width = $width;
	}

	/**
	 * Return the width of this column
	 */
	public function getWidth() {
		$caption = $this->getCaption();
		$capSize = mb_strwidth($caption, 'UTF-8');

		if ($this->width == 0) {
			switch ($this->type) {
				case PDO::PARAM_BOOL:
					$this->width = $capSize > 60 ? $capSize : 60;
					break;
					
				case PDO::PARAM_INT:
					$this->width = $capSize > 30 ? $capSize : 30;
					break;
					
				case PDO::PARAM_STR:
					$this->width = 250;
					break;
					
				case PDO::PARAM_LOB:
					$this->width = 500;
					break;
					
				default:
					$this->width = 250;
			}
		}

		return $this->width;
	}

	/**
	 * Set the alignment for this column
	 *
	 * @param string $alignment
	 *
	 */
	public function setAlignment($alignment) {
		$this->alignment = $alignment;
	}

	/**
	 * Return the alignment for this column
	 */
	public function getAlignment() {
		$alignment = $this->alignment;
		if (dsStringTools::isNullOrEmpty($alignment)) {
			$alignment = dsAlignmentConstants::ALIGN_CENTER;
		}

		return $alignment;
	}

	/**
	 * Set this column sortable
	 *
	 * @param bool $sortable
	 * 		TRUE, if column should be sortable
	 */
	public function setSortable($sortable = TRUE) {
		$this->sortable = $sortable;
	}

	/**
	 * Return if column is sortable
	 */
	public function isSortable() {
		return $this->sortable;
	}

	/**
	 * Set this column searchable
	 *
	 * @param bool $searchable
	 * 		TRUE, if column should be searchable
	 */
	public function setSearchable($searchable = TRUE) {
		$this->searchable = $searchable;
	}

	/**
	 * Return if column is searchable
	 */
	public function isSearchable() {
		return $this->searchable;
	}

	/**
	 * Sets this column as default search column
	 *
	 * @param bool $defaultSearchColumn
	 * 		TRUE, if this colum should be the default search column
	 */
	public function setDefaultSearchColumn($defaultSearchColumn = TRUE) {
		$this->defaultSearchColumn = $defaultSearchColumn;
	}

	/**
	 * Return if column is the default search column
	 */
	public function isDefaultSearchColumn() {
		return $this->defaultSearchColumn;
	}

	/**
	 * Hide this column
	 *
	 * @param bool $hide
	 * 		TRUE, if this column should be hidden
	 */
	public function setHide($hide = TRUE) {
		$this->hide = $hide;
	}

	/**
	 * Return if column is hidden
	 */
	public function isHidden() {
		return $this->hide;
	}

	/**
	 * Set a dsICellRenderer for this column
	 *
	 * @param dsICellRenderer $cellRenderer
	 * 		The dsICellRenderer for this column
	 */
	public function setCellRenderer(dsICellRenderer $cellRenderer) {
		$this->cellRenderer = $cellRenderer;
		$this->cellRenderer->setColumn($this);
	}

	/**
	 * Return the dsICellRenderer of this column
	 */
	public function getCellRenderer() {
		return $this->cellRenderer;
	}

	/**
	 * Print a JSON presentation of this column needed for
	 * searching features in the table
	 */
	public function printSearchColumn() {
		$code = "{ ";
		if ($this->getRelatedTable() == NULL) {
			$code .= "display: '" . $this->getCaption() . "', ";
		} else {
			$code .= "display: '" . $this->getRelatedTable() . "', ";
		}
		$code .= "name: '" . $this->getIdentifier() . "'";

		if ($this->isDefaultSearchColumn()) {
			$code .= ", isdefault: true";
		}

		$code .= " }";
		return $code;
	}

	/**
	 * Print a JSON presentation of this column (needed in the table)
	 */
	public function __toString() {
		$code = "{ ";
		if ($this->getRelatedTable() == NULL) {
			$code .= "display: '" . $this->getCaption() . "', ";
		} else {
			$code .= "display: '" . $this->getRelatedTable() . "', ";
		}

		$code .= "name: '" . $this->getIdentifier() . "', ";
		$code .= "width: " . $this->getWidth() . ", ";

		if ($this->isSortable()) {
			$code .= "sortable: true, ";
		} else {
			$code .= "sortable: false, ";
		}

		// hdie column
		if ($this->isHidden()) {
			$code .= "hide: true, ";
		} else {
			$code .= "hide: false, ";
		}

		$code .= "align: '" . $this->getAlignment() . "'";
		$code .= " }";

		return $code;
	}
}