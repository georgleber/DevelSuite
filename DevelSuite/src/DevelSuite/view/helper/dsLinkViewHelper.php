<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2011 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\view\helper;

use DevelSuite\dsApp;

/**
 * ViewHelper to handle linking.
 *
 * @package DevelSuite\view\helper
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsLinkViewHelper implements dsIViewHelper {
	/**
	 * Generate an internal link
	 *
	 * @param string $name
	 * 		Name of the route
	 * @param array $params
	 * 		Parameters needed in the action
	 */
	public function generateUrl($name, array $params = array()) {
		return dsApp::getRouter()->generateUrl($name, $params);
	}
}