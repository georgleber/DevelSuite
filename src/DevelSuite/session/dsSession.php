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

use DevelSuite\core\session\impl\dsDatabaseSession;

use DevelSuite\core\config\dsConfig;

/**
 * FIXME
 *
 * @package DevelSuite
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsSession {
	private static $sessHandler;

	/**
	 * Configures the session handling
	 */
	public static function configure() {
		ini_set('session.save_handler', 'user');
			
		$namespace = "\\DevelSuite\\session\\impl\\";
		$settings = dsConfig::read("session");

		$handler = NULL;
		switch ($settings['handler']) {
			case 'file':
				$handler = $namespace . "dsFileSessionHandler";
				throw new dsUnsupportedOperationException("File session handler is not implemented.");
				break;

			case 'databse':
				$handler = $namespace . "dsDatabaseSessionHandler";
				break;

			case 'cache':
				$handler = $namespace . "dsCacheSessionHandler";
				throw new dsUnsupportedOperationException("Cache session handler is not implemented.");
				break;

			case 'userdefined':
				$handler = $settings['userclass'];
				break;

			case 'php':
				return;
		}

		if(!class_exists($handler, FALSE)) {
			$classFile = "impl/" . $handler . ".php";
			require ($classFile);
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