<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\view\helper\impl;

use DevelSuite\dsApp;
use DevelSuite\view\helper\dsIViewHelper;

/**
 * ViewHelper to handle linking.
 *
 * @package DevelSuite\view\helper\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsLinkViewHelper implements dsIViewHelper {
	/**
	 * Generates an URL of the specified route name
	 *
	 * @param string $routeName
	 * 		Name of the route
	 * @param array $params
	 * 		Parameters, which are needed for the route
	 */
	public function generateUrl($routeName, array $params = array()) {
		$url = dsApp::getRouter()->generateUrl($routeName, $params);
		return $url;
	}

	/**
	 * Generate an internal HTML link
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
	public function generateLink($routeName, $linkText, array $attributes = array(), array $params = array()) {
		$link = $this->generateLinkStart($routeName, $attributes, $params);
		$link .= $linkText . "</a>";

		return $link;
	}

	/**
	 * Generate the start of an internal HTML link<br/>
	 * <b>IMPORTANT: The link will notcontain a terminating <pre><a/></pre></b>
	 *
	 * @param string $routeName
	 * 		Name of the route
	 * @param array $attributes
	 * 		Attributes for this link (e.g. styles, class, id, ...)
	 * @param array $params
	 * 		Parameters, which are needed for the route
	 */
	public function generateLinkStart($routeName, array $attributes = array(), array $params = array()) {
		$url = $this->generateUrl($routeName, $params);

		$link = "<a ";
		foreach ($attributes as $attr => $value) {
			$link .= $attr . "='" . $value . "' ";
		}
		$link .= "href='" . $url . "'>";

		return $link;
	}
}