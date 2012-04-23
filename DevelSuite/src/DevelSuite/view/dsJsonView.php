<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2011 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\view;

use DevelSuite\dsApp;

/**
 * View for rendering content as JSON.
 *
 * @package DevelSuite\view
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsJsonView extends dsAView {
	/**
	 * Constructor
	 *
	 * @param dsPageController $pageCtrl
	 * 		The PageController
	 */
	public function __construct() {
		dsApp::getResponse()->setContentType("application/json");
	}

	/**
	 * (non-PHPdoc)
	 * @see DevelSuite\view.dsAView::render()
	 */
	public function render() {
		echo json_encode($this->data);
	}
}