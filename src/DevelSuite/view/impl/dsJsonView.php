<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\view\impl;

use DevelSuite\dsApp;
use DevelSuite\view\dsAView;

/**
 * View for rendering content as JSON.
 *
 * @package DevelSuite\view\impl
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
	 * Overwrite the data array with self defined values. This method is
	 * useful in cases where the JSON is parsed by an external tool.
	 *
	 * @param array $data
	 * 		The new data array for teh JSON response
	 */
	public function setData($data) {
		$this->data = $data;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\view.dsAView::render()
	 */
	public function render() {
		$encode = json_encode($this->data);
		echo $encode;
	}
}