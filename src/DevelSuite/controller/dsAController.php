<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\controller;

use DevelSuite\routing\dsARoute;

use DevelSuite\controller\dsPageController;
use DevelSuite\routing\dsRoute;

use DevelSuite\view\dsAView;

use DevelSuite\dsApp;
use DevelSuite\exception\impl\dsDispatchException;
use DevelSuite\template\dsITemplate;

/**
 * Abstract super class for all (Document-)Controller.
 *
 * @package DevelSuite\controller
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
abstract class dsAController {
	private $route;

	/**
	 * The PageController
	 * @var dsPageController
	 */
	protected $pageCtrl;

	/**
	 * Process the requested action.
	 *
	 * @param dsPageController $pageCtrl
	 * 			The PageController object
	 * @param string $action
	 * 			The Action, which will be called
	 * @param array $parameters
	 * 			The needed parametes of the action
	 */
	public function processRequest(dsPageController $pageCtrl, dsARoute $route, $action) {
		$this->route = $route;
		$this->pageCtrl = $pageCtrl;

		$this->init();
		return $this->callAction($action);
	}

	/**
	 * Abtract method must be available in every controller to call the default action
	 */
	abstract public function defaultAction();

	/**
	 * Replacement of the constructor in order to configure the controller before calling an action
	 */
	protected function init() {}

	/**
	 * Call the given action of this controller instance
	 *
	 * @param string $action
	 * 			The action that should be called
	 * @param array $parameters
	 * 			The needed parameters of this action
	 * @return dsAView $view
	 * 			The view, which will be created by the action
	 * @throws dsDispatchException
	 */
	private function callAction($action) {
		if (method_exists($this, $action)) {
			$actionResult = call_user_func_array(array($this, $action), $this->route->getParameters());
		} else {
			throw new dsDispatchException(dsDispatchException::ACTION_NOT_CALLABLE);
		}

		if ($actionResult == NULL || !($actionResult instanceof dsAView)) {
			throw new dsDispatchException(dsDispatchException::ACTION_HAS_WRONG_RESULT);
		}

		return $actionResult;
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
		$this->pageCtrl->load($target, $params);
	}
}