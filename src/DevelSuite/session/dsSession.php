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

/**
 * FIXME
 *
 * @package DevelSuite\session
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
		$settings = dsConfig::read("session");

		$handler = NULL;
		switch ($settings['handler']) {
			case 'file':
				$handler = "\\DevelSuite\\session\\impl\\dsFileSessionHandler";
				throw new dsUnsupportedOperationException("File session handler is not implemented.");
				break;

			case 'databse':
				$handler = "\\DevelSuite\\session\\impl\\dsDatabaseSessionHandler";
				break;

			case 'cache':
				$handler = "\\DevelSuite\\session\\impl\\dsCacheSessionHandler";
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