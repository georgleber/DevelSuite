<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2011 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\controller;

use DevelSuite\dsApp;

use DevelSuite\config\dsConfig;
use DevelSuite\controller\filter\dsIFilter;
use DevelSuite\controller\filter\dsFilterChain;
use DevelSuite\routing\dsRouter;
use DevelSuite\util\dsStringTools;

/**
 * FrontController handles all request and resolves a route and delegates
 * the page load and controller handling to the <@see DevelSuite\controller\dsPageController>
 *
 * @package DevelSuite\controller
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsFrontController {
	/**
	 * FilterChain of all pre filter
	 * @var dsFilterChain
	 */
	private $preFilters;

	/**
	 * FilterChain of all post filters
	 * @var dsFilterChain
	 */
	private $postFilters;

	/**
	 * Class constructor - Creates the pre- and postfilter chains
	 */
	public function __construct() {
		$this->preFilters = new dsFilterChain();
		$this->postFilters = new dsFilterChain();
	}

	/**
	 * Add a new filter to the chain of pre-filters
	 *
	 * @param dsIFilter $filter
	 * 			The new pre-filter
	 */
	public function addPreFilter(dsIFilter $filter) {
		$this->preFilters->addFilter($filter);
	}

	/**
	 * Add a new filter to the chain of post-filters
	 *
	 * @param dsIFilter $filter
	 * 			The new post-filter
	 */
	public function addPostFilter(dsIFilter $filter) {
		$this->postFilters->addFilter($filter);
	}

	/**
	 * Dipatches the request by calling the PageController to load the layout
	 * and call the needed controllers. It offers the possibility to register
	 * pre and post filters running before and after dispatching.
	 */
	public function dispatch() {
		// run pre filter chain
		$this->preFilters->processFilters();

		try {
			if(!$this->compress()) {
				ob_start();
			}
			$pageCtrl = new dsPageController();
			$pageCtrl->dispatch();
		} catch(dsDispatchException $de) {

			# FIXME:
			# throw a DispatchException, if the controller could not be found
			# then show up a 404 Error Page
			# else if the request could not be processed by the controller
			# throw another excpetion in order to show up a message
		}

		// run post filter chain
		$this->postFilters->processFilters();

		// send response to client
		dsApp::getResponse()->send();
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