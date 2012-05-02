<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\view\helper\impl;

use DevelSuite\util\dsStringTools;
use DevelSuite\view\helper\dsIViewHelper;

/**
 * ViewHelper to handle text manipulatons.
 *
 * @package DevelSuite\view\helper\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsStringViewHelper implements dsIViewHelper {
	/**
	 * Trim the text to a special length and add ... to the end
	 * 
	 * @param string $text
	 * @param int $length
	 */
	public function trimText($text, $length) {
		return dsStringTools::truncate($text, $length);
	}
}