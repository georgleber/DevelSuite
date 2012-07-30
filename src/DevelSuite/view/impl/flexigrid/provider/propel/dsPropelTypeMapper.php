<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\view\impl\flexigrid\provider\propel;

use DevelSuite\view\impl\flexigrid\constants\dsColumnTypeConstants;

/**
 * Maps propel column types to their FlexiGrid corresponding types
 *
 * @package DevelSuite\view\impl\flexigrid\provider\propel
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsPropelTypeMapper {
	/**
	 * Mapping of propel types to FlexiGrid-types
	 * @var array
	 */
	private static $propelToFlexiMap = array(
		"CHAR"        	=> dsColumnTypeConstants::TYPE_STRING,
		"VARCHAR"     	=> dsColumnTypeConstants::TYPE_STRING,
		"LONGVARCHAR" 	=> dsColumnTypeConstants::TYPE_TEXT,
		"CLOB"        	=> dsColumnTypeConstants::TYPE_TEXT,
		"CLOB_EMU"    	=> dsColumnTypeConstants::TYPE_TEXT,
		"TINYINT"     	=> dsColumnTypeConstants::TYPE_INTEGER,
		"SMALLINT"    	=> dsColumnTypeConstants::TYPE_INTEGER,
		"INTEGER"     	=> dsColumnTypeConstants::TYPE_INTEGER,
		"BIGINT"      	=> dsColumnTypeConstants::TYPE_INTEGER,
		"DECIMAL"     	=> dsColumnTypeConstants::TYPE_DECIMAL,
		"NUMERIC"     	=> dsColumnTypeConstants::TYPE_DECIMAL,
		"REAL"        	=> dsColumnTypeConstants::TYPE_DECIMAL,
		"FLOAT"       	=> dsColumnTypeConstants::TYPE_DECIMAL,
		"DOUBLE"      	=> dsColumnTypeConstants::TYPE_DECIMAL,
		"BINARY"      	=> dsColumnTypeConstants::TYPE_STRING,
		"VARBINARY"   	=> dsColumnTypeConstants::TYPE_STRING,
		"LONGVARBINARY" => dsColumnTypeConstants::TYPE_TEXT,
		"BLOB"        	=> dsColumnTypeConstants::TYPE_TEXT,
		"DATE"        	=> dsColumnTypeConstants::TYPE_DATE,
		"TIME"	        => dsColumnTypeConstants::TYPE_DATE,
		"TIMESTAMP"   	=> dsColumnTypeConstants::TYPE_DATE,
		"BOOLEAN"     	=> dsColumnTypeConstants::TYPE_BOOLEAN,
		"BOOLEAN_EMU" 	=> dsColumnTypeConstants::TYPE_BOOLEAN
	);

	/**
	 * Looks up the propel type in the type map. If no mapping found, it returns STRING.
	 *
	 * @param string $propelType
	 * 		The propel type to map
	 */
	public static function mapPropelType($propelType) {
		$flexiType = NULL;
		if (isset(self::$propelToFlexiMap[$propelType])) {
			$flexiType = self::$propelToFlexiMap[$propelType];
		}

		if ($flexiType == NULL) {
			$flexiType = dsColumnTypeConstants::TYPE_STRING;
		}

		return $flexiType;
	}
}