<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\controller;

use DevelSuite\dsApp;
use DevelSuite\controller\dsPageController;
use DevelSuite\exception\impl\dsDispatchException;
use DevelSuite\routing\route\dsARoute;
use DevelSuite\view\dsAView;

/**
 * Abstract super class for all (Document-)Controller.
 *
 * @package DevelSuite\controller
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
abstract class dsAController {
	/**
	 * Corresponding route of this controller
	 * @var dsARoute
	 */
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
	 * Abtract method must be available in every controller to call the index action
	 */
	abstract public function indexAction();

	/**
	 * Replacement of the constructor in order to configure the controller before calling an action
	 */
	protected function init() {}

	/**
	 * Call the given action of this controller instance
	 *
	 * @param string $action
	 * 			The action that should be called
	 * @return dsAView $view
	 * 			The view, which will be created by the action
	 * @throws dsDispatchException
	 */
	private function callAction($action) {
		if (method_exists($this, $action) && is_callable(array($this, $action))) {
			$actionResult = call_user_func_array(array($this, $action), $this->route->getParameters());
		} else {
			throw new dsDispatchException(dsDispatchException::ACTION_NOT_CALLABLE, array($action));
		}

		if ($actionResult == NULL || !($actionResult instanceof dsAView)) {
			throw new dsDispatchException(dsDispatchException::WRONG_ACTIONRESULT, array(get_class($actionResult)));
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

	/**
	 * Redirects to a special route.
	 *
	 * @param string $routeName
	 * 		Name of the route
	 * @param array $parameters
	 *		Parameter for the redirect
	 * @param $immediately
	 * 		TRUE if the repsonse should directly redirected to the route
	 */
	public function redirect($routeName, array $parameters = array(), $immediately = FALSE) {
		$url = dsApp::getRouter()->generateUrl($routeName, $parameters);
		dsApp::getResponse()->redirectURL($url, $immediately);
	}

	/**
	 * Redirects directly
	 *
	 * @param string $routeName
	 * 		Name of the route
	 * @param array $parameters
	 *		Parameter for the redirect
	 */
	public function redirectImmediately($routeName, array $parameters = array()) {
		$this->redirect($routeName, $parameters, TRUE);
	}
}