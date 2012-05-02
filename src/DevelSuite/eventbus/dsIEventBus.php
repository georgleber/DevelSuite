<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\eventbus;

/**
 * Interface for subscribing, unsubscribing EventListeners and notifying
 * them on fired events.
 *
 * @package DevelSuite\eventbus
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
interface dsIEventBus {
	/**
	 * Register an EventListener for at least one event
	 *
	 * @param dsIEventSubscriber $subscriber
	 * 		The EventSubscriber to register
	 * @param mixed $eventNames
	 * 		Register the subscriber to all the events
	 */
	public function subscribe(dsIEventSubscriber $subscriber, $eventNames);

	/**
	 * Unregsiter an EventSubscriber from events (if $events is empty, it will be
	 * removed from all events)
	 *
	 * @param dsIEventSubscriber $subscriber
	 * 		The EventSubscriber to remove
	 * @param array $eventNames
	 * 		Remove the subscriber from all the events (if NULL remove complete)
	 */
	public function unsubscribe(dsIEventSubscriber $subscriber, $eventNames = NULL);

	/**
	 * Publish an event.
	 *
	 * @param string $eventName
	 * 		Name of the event
	 * @param dsEvent $event
	 * 		The event to publish
	 */
	public function publish($eventName, dsEvent $event = NULL);
}