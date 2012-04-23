<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\http;

use DevelSuite\util\dsStringTools;

/**
 * Represents a HTTP Request handling different HTTP methods
 * like GET, POST, PUT and DELETE.
 * It checks if magic_qoutes are enabled and strips the slashes
 * on the parameters.
 *
 * @package DevelSuite\http
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsRequest implements \ArrayAccess {
	/** TRUE, if request is a AJAX request */
	private $isAjaxRequest = FALSE;

	/** reference to the $_COOKIE data */
	private $cookie;

	/** reference to the $_FILE data */
	private $file;

	/** containing data of $_SERVER array */
	private $header;

	/** containing data of a HTTP Authentication */
	private $auth;

	/** method of this request */
	private $requestMethod = "GET";

	/** name of the module */
	private $moduleName;

	/** name of the controller */
	private $controllerName;

	/** name of the action */
	private $actionName;

	/** parameters */
	private $parameters;

	/**
	 * prevent creation of this object
	 */
	public function __construct() {
		// save $_SERVER vars to header array
		foreach($_SERVER as $key => $value) {
			if ($key === 'HTTP_X_REQUESTED_WITH' && $value === 'XMLHttpRequest') {
				$this->isAjaxRequest = TRUE;
			}

			if ($key === 'REQUEST_METHOD') {
				$this->requestMethod = $value;
			}

			$this->header[strtolower($key)] = $value;
		}

		if (isset($_SERVER["PHP_AUTH_USER"])) {
			$this->auth["user"] = $_SERVER["PHP_AUTH_USER"];
			$this->auth["pass"] = $_SERVER["PHP_AUTH_PW"];
		}

		switch ($this->requestMethod) {
			case 'POST':
				$this->parameters = $this->sanitizeMagicQuotes($_POST);
				$this->files = $this->sanitizeMagicQuotes($_FILES);
				break;

			case 'GET':
				$this->parameters = $this->sanitizeMagicQuotes($_GET);
				break;

			case 'PUT':
			case 'DELETE':
				//FIXME Handle REST
				break;
		}

		$this->cookie = $this->sanitizeMagicQuotes($_COOKIE);

	}

	/**
	 * Sanitizes variables GET, POST and COOKIE in case of magic quotes is set to on
	 */
	private function sanitizeMagicQuotes(array $params) {
		if(function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc()) {
			$sybaseOn = FALSE;
			if(ini_get('magic_quotes_sybase') && (strtolower(ini_get('magic_quotes_sybase')) != "off")) {
				$sybaseOn = TRUE;
			}

			$params = dsStringTools::stripslashesDeep($params, $sybaseOn);
		}

		return $params;
	}

	/**
	 * Set the name of the module
	 *
	 * @param string $moduleName
	 * 		name of the module
	 */
	public function setModuleName($moduleName) {
		$this->moduleName = $moduleName;
	}

	/**
	 * @return name of the module
	 */
	public function getModuleName() {
		return $this->moduleName;
	}

	/**
	 * Set the name of the controller
	 *
	 * @param string $controllerName
	 * 		name of the controller
	 */
	public function setControllerName($controllerName) {
		$this->controllerName = $controllerName;
	}

	/**
	 * @return name of the controller
	 */
	public function getControllerName() {
		return $this->controllerName;
	}

	/**
	 * Set the name of the action
	 *
	 * @param string $actionName
	 * 			name of the action
	 */
	public function setActionName($actionName) {
		$this->actionName = $actionName;
	}

	/**
	 * @return name of the action
	 */
	public function getActionName() {
		return $this->actionName;
	}

	/**
	 * @return HTTP Authentication data
	 */
	public function getAuthData() {
		return $this->auth;
	}

	/**
	 * Returns if this request is an AJAX request
	 */
	public function isAjaxRequest() {
		return $this->isAjaxRequest;
	}
	
	/**
	 * Returns the method of this request
	 */
	public function getRequestMethod() {
		return $this->requestMethod;
	}

	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetSet()
	 */
	public function offsetSet($offset, $value) {
		if (is_null($offset)) {
			$this->parameters[] = $value;
		} else {
			$this->parameters[$offset] = $value;
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetExists()
	 */
	public function offsetExists($offset) {
		return isset($this->parameters[$offset]);
	}

	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetUnset()
	 */
	public function offsetUnset($offset) {
		unset($this->parameters[$offset]);
	}

	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetGet()
	 */
	public function offsetGet($offset) {
		return isset($this->parameters[$offset]) ? $this->parameters[$offset] : null;
	}

	/**
	 * Check if key is set in the header paramters
	 *
	 * @param string $key
	 * 			key to check
	 *
	 * @return TRUE if key is set in the header parameters
	 */
	public function issetHeader($key) {
		$key = strtolower($key);
		return (isset($this->header[$key]));
	}

	/**
	 * Check if the key is set in the header parameters and return it
	 *
	 * @param string $key
	 * 		key to return
	 *
	 * @return if found the value for the key in the header paramters
	 */
	public function getHeader($key) {
		$key = strtolower($key);
		if($this->issetHeader($key)) {
			return $this->header[$key];
		} else {
			return NULL;
		}
	}

	/**
	 * Check if key is set in the file parameters
	 *
	 * @param string $key
	 * 			key to check
	 *
	 * @return TRUE if key is set in the file parameters
	 */
	public function issetFile($key) {
		return (isset($this->file[$key]));
	}

	/**
	 * Check if the key is set in the file parameters and return it
	 *
	 * @param string $key
	 * 		key to return
	 *
	 * @return if found the value for the key in the file paramters
	 */
	public function getFile($key) {
		if($this->issetFile($key)) {
			return $this->file[$key];
		} else {
			return NULL;
		}
	}

	/**
	 * Check if the file upload was successfull
	 *
	 * @param string $key
	 * 			key of the file
	 * @return TRUE, if file was uploaded successfully
	 */
	public function uploadFileSuccess($key) {
		return $this->issetFile($key) && $this->file[$key]["error"] == UPLOAD_ERR_OK;
	}

	/**
	 * Check if key is set in the cookie parameters
	 *
	 * @param string $key
	 * 			key to check
	 *
	 * @return TRUE if key is set in the cookie parameters
	 */
	public function issetCookie($key) {
		return (isset($this->cookie[$key]));
	}

	/**
	 * Check if the key is set in the cookie parameters and return it
	 *
	 * @param string $key
	 * 		key to return
	 *
	 * @return if found the value for the key in the cookie paramters
	 */
	public function getCookie($key) {
		if($this->issetCookie($key)) {
			return $this->cookie[$key];
		} else {
			return NULL;
		}
	}
}