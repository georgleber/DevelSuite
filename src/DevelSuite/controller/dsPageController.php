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
use DevelSuite\routing\dsInternalRoute;
use DevelSuite\view\dsAView;
use DevelSuite\dsApp;
use DevelSuite\config\dsConfig;
use DevelSuite\exception\impl\dsDispatchException;
use DevelSuite\http\dsRequest;
use DevelSuite\http\dsResponse;
use DevelSuite\routing\dsRoute;
use DevelSuite\routing\dsRouter;
use DevelSuite\util\dsStringTools;

/**
 * PageController handles the loading of the layout and the
 * load of embedded controller and templates
 *
 * @package DevelSuite\controller
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsPageController {
	/**
	 * The main view (needed to allow nested calls)
	 * @var dsAView
	 */
	private $rootView;

	/**
	 * The layout for the page
	 * @var string
	 */
	private $layout;

	/**
	 * Title of the page
	 * @var string
	 */
	private $title = "";

	/**
	 * Javascripts, which will be inserted in header
	 * @var array
	 */
	private $javascripts = array();

	/**
	 * Stylesheets, which will be inserted in header
	 * @var array
	 */
	private $stylesheets = array();

	/**
	 * Possibility to update the used layout
	 *
	 * @param string $newLayout
	 * 			The new layout
	 */
	public function updateLayout($newLayout) {
		$this->layout = $newLayout;
	}

	/**
	 * Returns the content of the root template, which is loaded by the root controller.
	 * This method will be called in the layout to include the main content.
	 */
	public function showContent() {
		$this->rootView->render();
	}

	/**
	 * Returns the title for the page
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Set title of the page
	 *
	 * @param string $title
	 * 			The title of the page
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Add a JavaScript include file to the content
	 *
	 * @param string $javaScript
	 * 			URL to the javascript file
	 */
	public function addJavaScript($javaScript) {
		if (!in_array($javaScript, $this->javascripts)) {
			$this->javascripts[] = $javaScript;
		}
	}

	/**
	 * Add a StyleSheet include file to the content
	 *
	 * @param string $styleSheet
	 * 			URL to the styleSheet file
	 * @param string $media
	 * 			StyleSheet used for which media [optional, default: screen, projection, tv]
	 */
	public function addStyleSheet($styleSheet, $media = NULL) {
		if (!in_array($styleSheet, $this->stylesheets)) {
			$this->stylesheets[] = array("style" => $styleSheet, "media" => $media);
		}
	}

	/**
	 * Dispatches the root controller and action, load the layout and render all templates
	 */
	public function dispatch() {
		$route = dsApp::getRoute();
		$this->rootView = $this->process($route);

		// if request is an ajax call, load only the template of the main controller and
		// set it in the repsonse object, otherwise call the render method to load
		// the full design
		$request = dsApp::getRequest();
		if($request->isAjaxRequest() || $request['ajaxFileUpload']) {
			$this->rootView->render();
			$content = ob_get_clean();
			dsApp::getResponse()->setContent($content);
		} else {
			$this->render();
		}
	}

	/**
	 * Load a embedded Controller/Action in the layout.
	 * This method can be called within a layout or template in order
	 * to embed another template, which needs input from a controller / action.
	 *
	 * The string could be of following format:
	 * - call controller and default action: <controller>
	 * - call controller with action: <controller>::<action>
	 * - call module-controller and default action: <module>/<controller>
	 * - call module-controller with action: <module>/<controller>::<action>
	 *
	 *	@param string $ctrlAction
	 * 			A string which defines a controller / action
	 *	@param string $params
	 * 			A string which defines a controller / action
	 */
	public function load($target, $params = array()) {
		$route = new dsInternalRoute($params);
		$route->parse($target);

		// process route and retrieve view
		$view = $this->process($route);

		// output template content
		$view->render();
	}

	/**
	 * Creates the output code for the javascript files
	 *
	 * @return $code
	 * 		Code containing the javascript includes
	 */
	public function includeJavaScripts() {
		$code = "";

		// if javascript is used add jquery
		$env = dsConfig::read("app.environment", "DEVELOPMENT");
		$jqueryVersion = dsConfig::read("app.jquery.version", "1.7.1");

		if ($env == "DEVELOPMENT") {
			$code .= "<script type='text/javascript' src='/public/scripts/jquery-" . $jqueryVersion . ".min.js'></script>\n";
		} else {
			$code .= "<script type='text/javascript' src='http://code.jquery.com/jquery-" . $jqueryVersion . ".min.js'></script>\n";
		}

		foreach($this->javascripts as $script) {
			$code .= "<script type='text/javascript' src='" . $script . "'></script>\n";
		}

		return $code;
	}

	/**
	 * Creates the output code for the stylesheet files
	 *
	 * @return $code
	 * 		Code containing the stylesheet includes
	 */
	public function includeStyleSheets() {
		$code = "";

		foreach($this->stylesheets as $styleSheet) {
			$code .= "<link rel='stylesheet' type='text/css' href='" . $styleSheet["style"] . "'";
			if ($styleSheet["media"] != NULL) {
				$code .= " media='" . $styleSheet["media"] . "'";
			}
			$code .= "/>\n";
		}

		return $code;
	}

	/**
	 * Process the controller and action of the route and retrieve the responsible view
	 *
	 * @param dsARoute $route
	 * 		Route to process
	 */
	private function process(dsARoute $route) {
		// resolve controller and check if action can be called
		$controller = $this->resolveController($route);
		$action = $this->resolveAction($controller, $route);

		// retrieve template
		return $controller->processRequest($this, $route, $action);
	}

	/**
	 * Renders the layout and include content and all embedded layouts.
	 * The content is set to the response object.
	 */
	private function render() {
		if (!isset($this->layout)) {
			$this->layout = APP_PATH . DS . "layout" . DS . "layout.tpl.php";
		}
		
		// try to load layout and include it
		if (file_exists($this->layout)) {
			include($this->layout);
			$content = ob_get_clean();
			dsApp::getResponse()->setContent($content);
		} else {
			throw new dsDispatchException(dsDispatchException::LAYOUT_NOT_FOUND);
		}
	}

	/**
	 * Resolves the controller by the controller name of the parsed route
	 *
	 * @throws dsDispatchException
	 */
	private function resolveController(dsARoute $route) {
		// load namespace of the controller
		// if module is set than the controller is in directory modules/<module>/controller/
		// if module is not set the controller is in directory controller/
		$namespace = NULL;
		if (dsStringTools::isFilled($route->getModule())) {
			$namespace = "\\modules\\" . $route->getModule() . "\\controller\\";
		} else {
			$namespace = "\\controller\\";
		}

		// load class and check if it is of type AController
		$class = $namespace . ucfirst($route->getController()) . "Controller";
		$controller = new $class();

		if ($controller instanceof dsAController) {
			return $controller;
		} else {
			throw new dsDispatchException(dsDispatchException::CONTROLLER_INVALID);
		}
	}

	/**
	 * Resolves the action of the loaded controller and the action name of the parsed route.
	 *
	 * @param dsAController $controller
	 * 		Loaded controller that should have the action
	 * @throws dsDispatchException
	 */
	private function resolveAction(dsAController $controller, dsARoute $route) {
		if (dsStringTools::isFilled($route->getAction())) {
			$action = $route->getAction() . "Action";
		} else {
			$action = "defaultAction";
		}

		// check if the action exists and if it is callable
		if (!method_exists($controller, $action) && !is_callable(array($controller, $action))) {
			throw new dsDispatchException(dsDispatchException::ACTION_UNDEFINED);
		}

		return $action;
	}

	/**
	 * Redirects internally.
	 *
	 * @param string $controller
	 * 		Name of hte controller
	 * @param string $action
	 * 		Name of the used action (if NULL, indexAction is used)
	 * @param array $additional_params
	 *		Additional parameter for the redirect
	 */
	public function redirect($controller, $module = NULL, $action = NULL, array $additionalParams = array(), $immediately = FALSE) {
		# $url = dsApp::getRouter()->generateUrl($controller, $module, $action, $additionalParams);
		# dsApp::getResponse()->redirectURL($url, $immediately);
	}

	/**
	 * Redirects directly and internally.
	 *
	 * @param string $controller
	 * 		Name of hte controller
	 * @param string $action
	 * 		Name of the used action (if NULL, indexAction is used)
	 * @param array $additional_params
	 *		Additional parameter for the redirect
	 */
	public function redirectImmediately($controller, $module = NULL, $action = NULL, array $additionalParams = array()) {
		self::redirect($controller, $module, $action, $additionalParams, TRUE);
	}
}