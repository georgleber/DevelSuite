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
use DevelSuite\config\dsConfig;
use DevelSuite\controller\dsFrontController;
use DevelSuite\exception\impl\dsRenderingException;
use DevelSuite\view\dsAView;

/**
 * View for rendering content as HTML.
 *
 * @package DevelSuite\view\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsHtmlView extends dsAView {
	/**
	 * The corresponding controller
	 * @var dsFrontController
	 */
	protected $ctrl;

	/**
	 * The template file
	 * @var string
	 */
	protected $template;

	/**
	 * Path to the view dir
	 * @var string
	 */
	protected $path;

	/**
	 * Constructor
	 *
	 * @param string $template
	 * 		Used template
	 * @param dsFrontController $ctrl
	 * 		The corresponding controller
	 */
	public function __construct($template, dsFrontController $ctrl) {
		$this->path = dsConfig::read("app.viewdir", APP_PATH . DS . "view");
		$this->template = $template;
		$this->ctrl = $ctrl;
	}

	/**
	 * Update path of the views
	 *
	 * @param string $path
	 * 		New path to lookup templates
	 */
	public function setPath($path) {
		$this->path = $path;
	}

	/**
	 * (non-PHPdoc)
	 * @see DevelSuite\view.dsAView::render()
	 */
	public function render() {
		$file = $this->path . DS . $this->template;

		if(file_exists($file)) {
			include($file);
		} else {
			throw new dsRenderingException(dsRenderingException::TEMPLATE_NOT_FOUND, array($file));
		}
	}

	/**
	 * Load another view from within the template
	 *
	 * @param string $target
	 * 		Target module/controller/action to call
	 * @param array $params
	 * 		Parameter needed to call action
	 */
	public function load($target, array $params = array()) {
		$this->ctrl->load($target, $params);
	}
}