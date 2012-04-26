<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
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
	 * @param string $routeName
	 * 		Name of the route
	 * @param string $linkText
	 * 		Name of the link
	 * @param array $attributes
	 * 		Attributes for this link (e.g. styles, class, id, ...)
	 * @param array $params
	 * 		Parameters, which are needed for the route
	 */
	public function generateUrl($routeName, $linkText, array $attributes = array(), array $params = array()) {
		$url = dsApp::getRouter()->generateUrl($routeName, $params);
		
		$link = "<a ";
		foreach ($attributes as $attr => $value) {
			$link .= $attr . "='" . $value . "' ";
		}
		$link .= "href='" . $url . "'>" . $linkText . "</a>";
		
		return $link;
	}
}