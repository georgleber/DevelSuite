<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\view\impl;

use DevelSuite\config\dsConfig;

use DevelSuite\dsApp;
use DevelSuite\controller\dsAController;
use DevelSuite\controller\dsPageController;
use DevelSuite\exception\impl\dsRenderingException;
use DevelSuite\view\dsAView;

/**
 * View for rendering mail content.
 *
 * @package DevelSuite\view\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsMailView extends dsAView {
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
	 * @param dsAController $ctrl
	 * 		The corresponding controller
	 */
	public function __construct($template) {
		$this->path = dsConfig::read("app.maildir", APP_PATH . DS . "mail");
		$this->template = $template;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\view.dsAView::render()
	 */
	public function render() {
		$file = $this->path . DS . $this->template;

		$content = NULL;
		if(file_exists($file)) {
			ob_start();
			include($file);
			$content = ob_get_contents();
			ob_end_clean();
		} else {
			throw new dsRenderingException(dsRenderingException::TEMPLATE_NOT_FOUND, array($file));
		}
		
		return $content;
	}
}