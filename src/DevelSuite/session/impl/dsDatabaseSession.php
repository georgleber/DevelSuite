<?php
/*
 * This file is part of the DevelSuite
* Copyright (C) 2012 Georg Henkel <info@develman.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
namespace DevelSuite\session\impl;

use DevelSuite\config\dsConfig;
use DevelSuite\session\impl\dsISessionManager;

use \PDO as PDO;
use \PDOException as PDOException;

/**
 * Class for handling sessions in a database.
 *
 * @package DevelSuite\session\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsDatabaseSession implements dsISessionManager {
	private $pdo;
	private $sessionTimeout;

	public function __construct() {
		ini_set("session.use_trans_sid", "true");

		$dsn = dsConfig::read('session.database.dsn');
		$user = dsConfig::read('session.database.user');
		$passwd = dsConfig::read('session.database.passwd');
		$this->pdo = new PDO($dsn, $user, $passwd);

		$timeout = dsConfig::read('session.timeout');
		$this->sessionTimeout = $timeout * 60;

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

		/*
		 * If cookies are disabled, use SSID and add it to all urls
		if (!isset($_COOKIE['PHPSESSID'])) {
		if (isset($_REQUEST['SSID']) && preg_match('~^[0-9a-f]{32}$~', $_REQUEST['SSID'])) {
		output_add_rewrite_var('SSID', $_REQUEST['SSID']);
		} else {
		output_add_rewrite_var('SSID', session_id());
		}
		}
		*/

		// make sure that session values are stored
		register_shutdown_function('session_write_close');
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\core\session.dsISessionManager::open()
	*/
	public function open($savePath, $sessionName) {
		return TRUE;
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\core\session.dsISessionManager::close()
	*/
	public function close() {
		// call the garbage collector
		return $this->gc(100);
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\core\session.dsISessionManager::read()
	*/
	public function read($id) {
		// create a query to get the session data
		$selectSQL = "SELECT * FROM ds_session WHERE session_id = :SESSION_ID
					AND user_agent = :USER_AGENT AND session_expire > :TIME";

		$selectStmt = $this->pdo->prepare($selectSQL);
		$selectStmt->bindParam(':SESSION_ID', $id);
		$selectStmt->bindParam(':USER_AGENT', $_SERVER['HTTP_USER_AGENT']);
		$selectStmt->bindParam(':TIME', time());
		$selectResult = $selectStmt->execute();

		// fetch result if exists
		$result = '';
		if ($selectStmt->rowCount() > 0) {
			$row = $selectStmt->fetch(PDO::FETCH_ASSOC);
			$result = $row["session_data"];
		}

		return $result;
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\core\session.dsISessionManager::write()
	*/
	public function write($id, $sessData) {
		$time = time() + $this->sessionTimeout;

		// check if some data was given
		if ($sessData == NULL) {
			return TRUE;
		}

		// update current session value
		$updateSQL = 'UPDATE ds_session SET session_expire = FROM_UNIXTIME(:SESSION_EXPIRE), session_data = :SESSION_DATA
					WHERE session_id = :SESSION_ID AND user_agent = :USER_AGENT';

		$updateStmt = $this->pdo->prepare($updateSQL);
		$updateStmt->bindParam(':SESSION_EXPIRE', $time);
		$updateStmt->bindParam(':SESSION_DATA', $sessData);
		$updateStmt->bindParam(':SESSION_ID', $id);
		$updateStmt->bindParam(':USER_AGENT', $_SERVER['HTTP_USER_AGENT']);
		$updateResult = $updateStmt->execute();

		// current session was updated
		if ($updateStmt->rowCount() > 0) {
			return TRUE;
		}

		// session does not exists create insert statement
		$insertSQL = 'INSERT INTO ds_session (session_id, user_agent, session_expire, date_created, session_data)
					VALUES (:SESSION_ID, :USER_AGENT, FROM_UNIXTIME(:SESSION_EXPIRE), FROM_UNIXTIME(:DATE_CREATED), :SESSION_DATA)';

		$insertStmt = $this->pdo->prepare($insertSQL);
		$insertStmt->bindParam(':SESSION_ID', $id);
		$insertStmt->bindParam(':USER_AGENT', $_SERVER['HTTP_USER_AGENT']);
		$insertStmt->bindParam(':SESSION_EXPIRE', $time);
		$insertStmt->bindParam(':DATE_CREATED', time());
		$insertStmt->bindParam(':SESSION_DATA', $sessData);
		$insertResult = $insertStmt->execute();

		return $insertResult;
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\core\session.dsISessionManager::destroy()
	*/
	public function destroy($id) {
		// create a query to delete a session
		$deleteSQL = 'DELETE FROM ds_session WHERE session_id = :SESSION_ID';

		$deleteStmt = $this->pdo->prepare($deleteSQL);
		$deleteStmt->bindParam(':SESSION_ID', $id);
		$deleteResult = $deleteStmt->execute();

		return $deleteResult;
	}

	/* (non-PHPdoc)
	 * @see DevelSuite\core\session.dsISessionManager::gc()
	*/
	public function gc($maxlifetime) {
		/* period after that a session pass off */
		$maxlifetime = time() - $this->sessionTimeout;

		// delete statement
		/*
		$selectSQL = 'SELECT * FROM ds_session WHERE session_expire < FROM_UNIXTIME(:MAXLIFETIME)';

		$selectStmt = $this->pdo->prepare($selectSQL);
		$selectStmt->bindParam(':MAXLIFETIME', $maxlifetime);
		$selectResult = $selectStmt->execute();

		while ($row = $selectStmt->fetch(PDO::FETCH_ASSOC)) {
		$expire = $row['session_expire'];
		echo "FOUND: " . date('d.m.Y H:i:s', $expire) . "<br/>";
		}
		*/

		// delete statement
		$deleteSQL = 'DELETE FROM ds_session WHERE session_expire < FROM_UNIXTIME(:MAXLIFETIME)';

		$deleteStmt = $this->pdo->prepare($deleteSQL);
		$deleteStmt->bindParam(':MAXLIFETIME', $maxlifetime);
		$deleteResult = $deleteStmt->execute();

		return $deleteResult;
	}
}