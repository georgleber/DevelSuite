<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\routing\route;

/**
 * Abstract super class for all Routes.
 *
 * @package DevelSuite\routing\route
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
abstract class dsARoute {
	/**
	 * Used module
	 * @var string
	 */
	protected $module;

	/**
	 * Coresponding controller for this route
	 * @var string
	 */
	protected $controller;

	/**
	 * The action which will be called
	 * @var string
	 */
	protected $action;

	/**
	 * Used parameters of this route
	 * @var array
	 */
	protected $parameters = array();

	/**
	 * Returns the used module
	 */
	public function getModule() {
		return $this->module;
	}

	/**
	 * Returns the controller of this route
	 */
	public function getController() {
		return $this->controller;
	}

	/**
	 * Return the action of the corresponding controller
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * Returns the parameters defined with this route
	 */
	public function getParameters () {
		return $this->parameters;
	}

	/**
	 * Parses the target to a defined route
	 *
	 * @param string $target
	 * 		Target to parse
	 */
	public abstract function parse($target);
}