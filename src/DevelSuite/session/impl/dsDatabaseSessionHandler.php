<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\session\impl;

use DevelSuite\session\dsASessionHandler;

use DevelSuite\config\dsConfig;
use DevelSuite\session\impl\dsISessionManager;

use \PDO as PDO;
use \PDOException as PDOException;

/**
 * FIXME
 * Class for handling sessions in a database.
 *
 * @package DevelSuite\session\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsDatabaseSessionHandler extends dsASessionHandler {
	protected function init() {
		
	}
	
	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\session.dsASessionHandler::open()
	 */
	public function open($savePath, $sessionName) {
		return TRUE;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\session.dsASessionHandler::close()
	 */
	public function close() {
		// call the garbage collector
		return TRUE;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\session.dsASessionHandler::read()
	 */
	public function read($sessionId) {
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

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\session.dsASessionHandler::write()
	 */
	public function write($sessionId, $data) {
		$time = time();

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

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\session.dsASessionHandler::destroy()
	 */
	public function destroy($sessionId) {
		// create a query to delete a session
		$deleteSQL = 'DELETE FROM ds_session WHERE session_id = :SESSION_ID';

		$deleteStmt = $this->pdo->prepare($deleteSQL);
		$deleteStmt->bindParam(':SESSION_ID', $id);
		$deleteResult = $deleteStmt->execute();

		return $deleteResult;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\session.dsASessionHandler::gc()
	 */
	public function gc($lifetime) {
		// overwrite with the predefined sessionLifetime
		$maxlifetime = time() - $this->sessionLifetime;
			
		// delete statement
		$deleteSQL = 'DELETE FROM ds_session WHERE session_expire < FROM_UNIXTIME(:MAXLIFETIME)';

		$deleteStmt = $this->pdo->prepare($deleteSQL);
		$deleteStmt->bindValue(':MAXLIFETIME', $maxlifetime, PDO::PARAM_STR);
		$deleteResult = $deleteStmt->execute();

		return $deleteResult;
	}
}