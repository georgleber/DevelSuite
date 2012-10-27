<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form\view;

use DevelSuite\exception\impl\dsRenderingException;
use DevelSuite\view\dsAView;

/**
 * View that renders a pre-defined template with a form.
 *
 * @package DevelSuite\view\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsFormView extends dsAView {
	/**
	 * Loads the form template, assigns all information to it and renders it
	 */
	public function render() {
		$template = "form.tpl.php";
		$path = dirname(__FILE__) . DS . "form" . DS . "tpl";
		
		$content = NULL;
		$file = $path . DS . $template;
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