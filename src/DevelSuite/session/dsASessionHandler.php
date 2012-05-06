<?php
namespace DevelSuite\session;

use DevelSuite\config\dsConfig;

abstract class dsASessionHandler {
	protected $sessionLifetime;

	public function __construct() {
		// load session timeout
		$this->sessionLifetime = dsConfig::read('session.maxlifetime');
		if ($this->sessionLifetime != NULL) {
			$this->sessionLifetime *= 60;
		} else {
			$this->sessionLifetime = get_cfg_var('session.gc_maxlifetime');
		}

		session_set_save_handler(
		array(&$this, 'open'),
		array(&$this, 'close'),
		array(&$this, 'read'),
		array(&$this, 'write'),
		array(&$this, 'destroy'),
		array(&$this, 'gc')
		);

		// start a new session
		session_start();

		// make sure that session values are stored
		register_shutdown_function('session_write_close');
	}

	/**
	 * Regenerates the session id and deletes the old session.
	 */
	public static function regenerate_id() {
		// regenerates the id this function will create a new session,
		// with a new id and containing the data from the old session
		// but will not delete the old session
		session_regenerate_id(TRUE);
	}

	abstract public function open($savePath, $sessionName);
	abstract public function close();
	abstract public function read($sessionID);
	abstract public function write($sessionID, $sessionData);
	abstract public function destroy($sessionID);
	abstract public function gc();
}