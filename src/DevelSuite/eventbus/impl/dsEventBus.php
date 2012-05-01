<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\eventbus\impl;

use DevelSuite\eventbus\dsEvent;
use DevelSuite\eventbus\dsIEventBus;
use DevelSuite\eventbus\dsIEventSubscriber;

/**
 * EventBus handles all subscription and unsubscription of any dsIEventSubscriber.
 * It also gives the possibility to inform all subscribers of an event.
 *
 * @package DevelSuite\eventbus\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsEventBus implements dsIEventbus {
	/**
	 * Map of all subscribers of the events
	 * @var array
	 */
	private $subscriberMap = array();

	/**
	 * (non-PHPdoc)
	 * @see DevelSuite\eventbus.dsIEventbus::subscribe()
	 */
	public function subscribe(dsIEventSubscriber $subscriber, $eventNames) {
		if ($eventNames != NULL) {
			if (!is_array($eventNames)) {
				$eventNames = array($eventNames);
			}

			foreach ($eventNames as $eventName) {
				if (!array_key_exists($eventName, $this->subscriberMap)) {
					$this->subscriberMap[$eventName] = array();
				}

				array_push($this->subscriberMap[$eventName], $subscriber);
			}
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see DevelSuite\eventbus.dsIEventbus::unsubscribe()
	 */
	public function unsubscribe(dsIEventSubscriber $subscriber, $eventNames = NULL) {
		if ($eventNames == NULL) {
			$eventNames = array_keys($this->subscriberMap);
		} else if (!is_array($eventNames)) {
			$eventNames = array($eventNames);
		}

		// remove listener from list
		foreach ($eventNames as $eventName) {
			if (($key = array_search($subscriber, $this->subscriberMap[$eventName])) !== FALSE) {
				unset($this->subscriberMap[$eventName][$key]);
			}
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see DevelSuite\eventbus.dsIEventbus::publish()
	 */
	public function publish(dsEvent $event) {
		if (array_key_exists($event->getName(), $this->subscriberMap)) {
			foreach ($this->subscriberMap[$event->getName()] as $subscriber) {
				$subscriber->onEvent($event);
			}
		}
	}
}