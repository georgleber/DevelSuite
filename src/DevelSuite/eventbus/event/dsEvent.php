<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\eventbus\event;

/**
 * Event holds information like name and publisher
 * for the EventSubscriber
 *
 * @package DevelSuite\eventbus
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsEvent {
	/**
	 * Name of the event
	 * @var string
	 */
	private $name;

	/**
	 * Publisher of the event
	 * @var string
	 */
	private $publisher;

	/**
	 * Constructor
	 *
	 * @param string $name
	 * 		Name of the event
	 * @param string $publisher
	 * 		Publisher of the event
	 */
	public function __construct($name, $publisher) {
		$this->name = $name;
		$this->publisher = $publisher;
	}

	/**
	 * Returns the name of this event
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Returns the publisher of this event
	 */
	public function getPublisher() {
		return $this->publisher;
	}
}