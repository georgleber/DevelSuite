<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\eventbus\event;

use \Exception as Exception;

/**
 * Event holds information like name, publisher and also the
 * exception, which is responsible for creation of this event.
 *
 * @package DevelSuite\eventbus
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */

class dsExceptionEvent extends dsEvent {
	/**
	 * Exception for which this event is created
	 * @var Exception
	 */
	private $exception;

	/**
	 * Constructor
	 *
	 * @param string $name
	 * 		Name of the event
	 * @param string $publisher
	 * 		Publisher of the event
	 * @param Exception $exception
	 * 		Exception of this event
	 */
	public function __construct($name, $publisher, Exception $exception) {
		parent::__construct($name, $publisher);

		$this->exception = $exception;
	}

	/**
	 * Return the exception of this event
	 */
	public function getException() {
		return $this->exception;
	}
}