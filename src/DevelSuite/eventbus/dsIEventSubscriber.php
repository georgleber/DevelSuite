<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\eventbus;

use DevelSuite\eventbus\event\dsEvent;

/**
 * Interface for all EventSubscriber to handle fired events.
 *
 * @package DevelSuite\eventbus
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
interface dsIEventSubscriber {
	/**
	 * This method is called by the EventBus to inform the
	 * EventSubscriber of an event.
	 *
	 * @param dsEvent $event
	 * 		The fired event
	 */
	public function onEvent(dsEvent $event);
}