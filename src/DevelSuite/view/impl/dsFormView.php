<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\view\impl;

/**
 * View that renders a pre-defined template with a form.
 *
 * @package DevelSuite\view\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
use DevelSuite\form\dsForm;

class dsFormView extends dsHtmlView {
	/**
	 * Callback to view, when action was successfull
	 * @var string
	 */
	private $callbackUrl;
	
	/**
	 * The form to render
	 * @var dsForm
	 */
	private $form;

	/**
	 * Constructor
	 *
	 * @param string $template
	 * 		Used template
	 * @param dsFrontController $ctrl
	 * 		The corresponding controller
	 * @param string $callbackUrl
	 * 		Callback for actions
	 */
	public function __construct($template, dsFrontController $frontCtrl, $callbackUrl) {
		parent::__construct($template, $frontCtrl);

		$this->callbackUrl = $callbackUrl;
	}
	
	/**
	 * Set the corresponding form
	 *
	 * @param dsForm $form
	 * 		The form to show in template
	 */
	public function setForm(dsForm $form) {
		$this->form = $form;
	}

	/**
	 * Loads the form template, assigns all information to it and renders it
	 */
	public function doLayout() {
		$formView = new dsHtmlView("form.tpl.php", $this->ctrl);
		$formView->setPath(dirname(__FILE__) . DS . "form" . DS . "tpl");

		$formView->assign("callbackUrl", $this->callbackUrl)
		->assign("form", $form);
		return $formView->render();
	}
}