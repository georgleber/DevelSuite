<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\session;

use DevelSuite\config\dsConfig;
use DevelSuite\exception\impl\dsSessionException;
use DevelSuite\exception\spl\dsUnsupportedOperationException;

/**
 * Defines constants for the different handlers and load the configured one
 *
 * @package DevelSuite\session
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsSession {
	const HANDLER_PHP 		= 100;
	const HANDLER_DATABASE 	= 200;
	const HANDLER_FILE 		= 300;
	const HANDLER_CACHE		= 400;
	const HANDLER_USER 		= 500;

	/**
	 * Configures the session handling
	 */
	public static function configure() {
		ini_set('session.save_handler', 'user');
		$settings = dsConfig::read("session");

		$handler = NULL;
		switch ($settings['handler']) {
			case self::HANDLER_PHP:
				return;

			case self::HANDLER_DATABASE:
				$handler = "\\DevelSuite\\session\\impl\\dsDatabaseSessionHandler";
				break;

			case self::HANDLER_FILE:
				$handler = "\\DevelSuite\\session\\impl\\dsFileSessionHandler";
				throw new dsUnsupportedOperationException("File session handler is not implemented.");
				break;

			case self::HANDLER_CACHE:
				$handler = "\\DevelSuite\\session\\impl\\dsCacheSessionHandler";
				throw new dsUnsupportedOperationException("Cache session handler is not implemented.");
				break;

			case self::HANDLER_USER:
				$handler = $settings['userclass'];
				break;
		}

		if (class_exists($handler)) {
			$sessionHandler = new $handler();
			if (!($sessionHandler instanceof dsASessionHandler)) {
				throw new dsSessionException(dsSessionException::HANDLER_INSTANTIATION_ERROR, array($handler));
			}
		} else {
			throw new dsSessionException(dsSessionException::HANDLER_NOT_FOUND, array($handler));
		}
	}
}