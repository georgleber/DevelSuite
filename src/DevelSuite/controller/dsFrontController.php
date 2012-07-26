<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\controller;

use DevelSuite\exception\dsErrorCodeException;

use DevelSuite\dsApp;
use DevelSuite\config\dsConfig;
use DevelSuite\eventbus\dsEvent;
use DevelSuite\exception\impl\dsDispatchException;
use DevelSuite\exception\impl\dsRenderingException;
use DevelSuite\http\dsRequest;
use DevelSuite\http\dsResponse;
use DevelSuite\routing\route\dsARoute;
use DevelSuite\routing\route\dsInternalRoute;
use DevelSuite\routing\dsRouter;
use DevelSuite\util\dsStringTools;
use DevelSuite\view\dsAView;

/**
 * FrontController handles all request, resolves a route,
 * the loading of the layout and of embedded controllers and templates
 *
 * @package DevelSuite\controller
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsFrontController {
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
	 * Dispatches the root controller and action, load the layout and render all templates.
	 * EventSubscriber can be registered for following events, which are called before / after
	 * the routing and rendering action:
	 * <ul>
	 * 	<li>system.dispatching.prefilter</li>
	 * 	<li>system.dispatching.postfilter</li>
	 * </ul>
	 */
	public function dispatch() {
		// notify all pre filter
		dsApp::getEventBus()->publish("system.dispatching.prefilter");

		if(!$this->compress()) {
			ob_start();
		}

		try {
			$route = dsApp::getRoute();
			$this->rootView = $this->process($route);

			// if request is an ajax call, load only the template of the main controller and
			// set it in the repsonse object, otherwise call the render method to load
			// the full design
			$request = dsApp::getRequest();
			if($request->isAjaxRequest() || $request['ajaxFileUpload']) {
				$this->rootView->render();
			} else {
				$this->render();
			}
		} catch(dsDispatchException $e) {
			echo "dispatch exception occured " . $e;
			# FIXME:
			# throw a DispatchException, if the controller could not be found
			# then show up a 404 Error Page
			# else if the request could not be processed by the controller
			# throw another excpetion in order to show up a message
		} catch (dsErrorCodeException $e) {
			echo "error code exception occured " . $e;
		} catch (\Exception $e) {
			echo "exception occured " . $e;
		}

		// notify all post filter
		dsApp::getEventBus()->publish("system.dispatching.postfilter");

		// send response to client
		dsApp::getResponse()->send();
	}

	
	public function passThru(dsAView $view) {
		$view->render();
		
		// send response to client
		dsApp::getResponse()->send();
		
		exit();
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
	 * Returns the content of the root template, which is loaded by the root controller.
	 * This method will be called in the layout to include the main content.
	 */
	public function showContent() {
		$this->rootView->render();
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
	 * Creates the output code for the javascript files
	 *
	 * @return $code
	 * 		Code containing the javascript includes
	 */
	public function includeJavaScripts() {
		$code = "";

		$code .= "<script type='text/javascript' src='http://code.jquery.com/jquery.min.js'></script>";
		$code .= "<script>window.jQuery || document.write('<script src=\"/public/scripts/jquery.min.js\"><\/script>')</script>";

		foreach($this->javascripts as $script) {
			$code .= "<script type='text/javascript' src='" . $script . "'></script>\n";
		}

		return $code;
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
	 * Is used to call an action of a ViewHelper.
	 *
	 * @param string $method
	 * 		(Short-)Name of the ViewHelper
	 * @param array $arguments
	 * 		First argument is the action the rest are the
	 * 		arguments needed by the action
	 */
	public function __call($method, $arguments) {
		$viewHelper = dsApp::getViewHelperCache()->lookup($method);

		// first argument is action name
		$action = $arguments[0];
		$params = array_slice($arguments, 1);

		$result = NULL;
		if (method_exists($viewHelper, $action) && is_callable(array($viewHelper, $action))) {
			$result = call_user_func_array(array($viewHelper, $action), $params);
		} else {
			throw new dsRenderingException(dsRenderingException::ACTION_NOT_CALLABLE, array($action, get_class($viewHelper)));
		}

		return $result;
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
		} else {
			throw new dsRenderingException(dsRenderingException::LAYOUT_NOT_FOUND);
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
			throw new dsDispatchException(dsDispatchException::CONTROLLER_INVALID, array($route->getController()));
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
			$action = "indexAction";
		}

		// check if the action exists and if it is callable
		if (!method_exists($controller, $action)) {
			$action = "indexAction";
		} else if (!is_callable(array($controller, $action))) {
			throw new dsDispatchException(dsDispatchException::ACTION_NOT_CALLABLE, array($action));
		}

		return $action;
	}

	/**
	 * Sets the correct output buffering handler to send a compressed response. Responses will
	 * be compressed with zlib, if the extension is available.
	 *
	 * @return boolean FALSE if client does not accept compressed responses or
	 * 				no handler is available, true otherwise
	 */
	private function compress() {
		if(dsConfig::read("app.compressoutput") == TRUE) {
			$accEncoding = dsApp::getRequest()->getHeader('http_accept_encoding');
			$compressionEnabled = FALSE;

			if (ini_get("zlib.output_compression") !== '1' && extension_loaded("zlib") && (strpos($accEncoding, 'gzip') !== FALSE)) {
				$compressionEnabled = TRUE;
			}

			return $compressionEnabled && ob_start('ob_gzhandler');
		}

		return FALSE;
	}
}