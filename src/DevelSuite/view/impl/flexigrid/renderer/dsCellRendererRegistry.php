<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\view\impl\flexigrid\renderer;

use DevelSuite\view\impl\flexigrid\model\dsColumnTypes;

/**
 * FIXME
 *
 * @package DevelSuite\view\impl\flexigrid\renderer
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsCellRendererRegistry {
	private $rendererMap = array();

	public function __construct() {
		$this->rendererMap[dsColumnTypes::COLUMNTYPE_STRING] = new dsStringCellRenderer();
		$this->rendererMap[dsColumnTypes::COLUMNTYPE_TEXT] = new dsStringCellRenderer();
		$this->rendererMap[dsColumnTypes::COLUMNTYPE_INTEGER] = new dsIntegerCellRenderer();
		$this->rendererMap[dsColumnTypes::COLUMNTYPE_DECIMAL] = new dsDecimalCellRenderer();
		$this->rendererMap[dsColumnTypes::COLUMNTYPE_DATE] = new dsDateCellRenderer();
		$this->rendererMap[dsColumnTypes::COLUMNTYPE_BOOLEAN] = new dsBooleanCellRenderer();
	}

	public function setCellRenderer($columnType, dsICellRenderer $renderer) {
		$this->rendererMap[$columnType]	= $renderer;
	}

	public function getCellRenderer($columnType) {
			if (isset($this->rendererMap[$columnType])) {
		return $this->rendererMap[$columnType];
		} else {
			return new dsDefaultCellRenderer();
		}
	}
}