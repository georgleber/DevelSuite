<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2011 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\session\impl;

/**
 * Interface for a consistent session handling.
 *
 * @package DevelSuite\session\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
interface dsISessionManager {
	/**
	 * Is called to open a session.
	 *
	 * @access public
	 * @param string $savePath
	 * 			The save path
	 * @param string $sessionName
	 * 			The name of the session
	 * @return bool
	 */
	public function open($savePath, $sessionName);

	/**
	 * Is called when the reading in a session is
	 * completed. The method calls the garbage collector.
	 *
	 * @return bool
	 */
	public function close();

	/**
	 * Is called to read data from a session.
	 *
	 * @param int $id
	 * 			The id of the current session
	 * @return Mixed
	 */
	public function read($id);

	/**
	 * Writes data into a session.
	 *
	 * @param int $id
	 * 			The id of the current session
	 * @param string $sessData
	 * 			The data of the session
	 * @return bool
	 */
	public function write($id, $sessData);

	/**
	 * Ends a session and deletes it.
	 *
	 * @param int $id
	 * 		The id of the current session
	 * @return bool
	 */
	public function destroy($id);

	/**
	 * The garbage collector deletes all sessions
	 * that where not deleted by the session_destroy function.
	 *
	 * @param int $maxlifetime
	 * 			The maximum session lifetime
	 * @return bool
	 */
	public function gc($maxlifetime);
}