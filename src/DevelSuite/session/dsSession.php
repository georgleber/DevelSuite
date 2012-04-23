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
use DevelSuite\session\impl\dsDatabaseSession;

/**
 * Class for configuring the session handling.
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
			
		$namespace = "DevelSuite\\core\\session\\impl\\";
		$appHandler = dsConfig::read('session.save');

		$handler = NULL;
		switch ($appHandler) {
			case 'php':
				return;

			case 'database':
				$handler = 'dsDatabaseSession';
				break;
		}

		$clazz = $namespace . $handler;
		if(!class_exists($clazz, FALSE)) {
			$classFile = "impl/" . $handler . ".php";
			require ($classFile);
		}
		
		if (class_exists($clazz)) {
			self::$sessHandler = new $clazz();
		}
	}

	/**
	 * Regenerates the session id and deletes the old session.
	 */
	public static function regenerate_id() {
		if (isset(self::$sessHandler)) {
			// saves the old session's id
			$oldSessionID = session_id();

			// regenerates the id this function will create a new session,
			// with a new id and containing the data from the old session
			// but will not delete the old session
			session_regenerate_id();

			// because the session_regenerate_id() function does not delete the old session,
			// we have to delete it manually
			self::$sessHandler->destroy($oldSessionID);
		}
	}

}