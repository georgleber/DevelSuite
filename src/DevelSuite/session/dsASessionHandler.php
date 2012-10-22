<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\session;

use Monolog\Handler\StreamHandler;

use Monolog\Logger;

use DevelSuite\config\dsConfig;

/**
 * Abstract class for all SessionHandler, for initializing
 * all needed information.
 *
 * @package DevelSuite\session
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
abstract class dsASessionHandler {
	/**
	 * The responsible logger
	 * @var Logger
	 */
	protected $log;
	
	/**
	 * Maximum Lifetime of a session
	 * @var string
	 */
	protected $sessionLifetime;

	/**
	 * Constructor
	 */
	public function __construct() {
		ini_set('session.save_handler', 'user');
		
		// load session timeout
		$this->sessionLifetime = dsConfig::read('session.maxlifetime');
		if ($this->sessionLifetime != NULL) {
			$this->sessionLifetime *= 60;
		} else {
			$this->sessionLifetime = get_cfg_var('session.gc_maxlifetime');
		}
		
		$this->log = new Logger("ASessionHandler");
		$this->log->pushHandler(new StreamHandler(LOG_PATH . DS . "server.log"));
		
		// init
		$this->init();

		// init session save handler
		$this->setSaveHandler();

		// start a new session
		session_start();

		// make sure that session values are stored
		register_shutdown_function('session_write_close');
	}

	/**
	 * Can be used by subclasses to initialize further information
	 */
	protected abstract function init();

	/**
	 * Sets the SaveHandle-Methods for the session
	 */
	private function setSaveHandler() {
		session_set_save_handler(
		array(&$this, 'open'),
		array(&$this, 'close'),
		array(&$this, 'read'),
		array(&$this, 'write'),
		array(&$this, 'destroy'),
		array(&$this, 'gc')
		);
	}

	/**
	 * Regenerates the session id and deletes the old session.
	 */
	public static function regenerate_id() {
		// saves the old session's id
		$oldSessionID = session_id();

		// regenerates the id this function will create a new session,
		// with a new id and containing the data from the old session
		// but will not delete the old session
		session_regenerate_id();

		// because the session_regenerate_id() function does not delete the old session,
		// we have to delete it manually
		self::destroy($oldSessionID);
	}

	/**
	 * The open callback works like a constructor in classes and is executed when the session
	 * is being opened. It is the first callback function executed when the session is started
	 * automatically or manually with session_start(). Return value is TRUE for success, FALSE for failure.
	 *
	 * @param string $savePath
	 * 		Path, where session is stored
	 * @param string $sessionName
	 * 		Name of the session
	 * @return bool
	 * 		TRUE for success, FALSE for failure
	 */
	abstract public function open($savePath, $sessionName);

	/**
	 * The close callback works like a destructor in classes and is executed
	 * after the session write callback has been called. It is also invoked when
	 * session_write_close() is called. Return value should be TRUE for success, FALSE for failure.
	 *
	 * @return bool
	 * 		TRUE for success, FALSE for failure
	 */
	abstract public function close();

	/**
	 * The read callback must always return a session encoded (serialized) string, or an empty string if
	 * there is no data to read. This callback is called internally by PHP when the session starts or when
	 * session_start() is called. Before this callback is invoked PHP will invoke the open callback.
	 * The value this callback returns must be in exactly the same serialized format that was originally
	 * passed for storage to the write callback. The value returned will be unserialized automatically by PHP
	 * and used to populate the $_SESSION superglobal. While the data looks similar to serialize() please note
	 * it is a different format which is speficied in the session.serialize_handler ini setting.
	 *
	 * @param string $sessionId
	 * 		ID of the Session
	 * @return string
	 * 			session encoded (serialized) string, or empty string
	 */
	abstract public function read($sessionId);

	/**
	 * The write callback is called when the session needs to be saved and closed. This callback receives the
	 * current session ID a serialized version the $_SESSION superglobal. The serialization method used internally
	 * by PHP is specified in the session.serialize_handler ini setting.
	 * The serialized session data passed to this callback should be stored against the passed session ID. When
	 * retrieving this data, the read callback must return the exact value that was originally passed to the write callback.
	 * This callback is invoked when PHP shuts down or explicitly when session_write_close() is called. Note that after
	 * executing this function PHP will internally execute the close callback.
	 *
	 * @param string $sessionId
	 * 		ID of the session
	 * @param string $data
	 * 		Data, which should be saved in Session
	 */
	abstract public function write($sessionId, $data);

	/**
	 * This callback is executed when a session is destroyed with session_destroy() or with session_regenerate_id() with
	 * the destroy parameter set to TRUE. Return value should be TRUE for success, FALSE for failure.
	 *
	 * @param string $sessionId
	 * 		ID of the session
	 * @return bool
	 * 		TRUE for success, FALSE for failure
	 */
	abstract public function destroy($sessionId);

	/**
	 * The garbage collector callback is invoked internally by PHP periodically in order to purge old session data.
	 * The frequency is controlled by session.gc_probability and session.gc_divisor. The value of lifetime which is
	 * passed to this callback can be set in session.gc_maxlifetime. Return value should be TRUE for success, FALSE for failure.
	 *
	 * @param string $lifetime
	 * 		Maximum lifetime of session
	 * @return bool
	 * 		TRUE for success, FALSE for failure
	 */
	abstract public function gc($lifetime);
}