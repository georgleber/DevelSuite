<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\session\impl;

use DevelSuite\dsApp;

use DevelSuite\exception\impl\dsSessionException;

use DevelSuite\util\dsStringTools;

use DevelSuite\session\dsASessionHandler;

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
class dsDatabaseSessionHandler extends dsASessionHandler {
	/**
	 * PDO for handling all database queries
	 * @var PDO
	 */
	private $pdo;

	/**
	 * Name of the table used for sessions
	 * @var string
	 */
	private $tableName;

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\session.dsASessionHandler::init()
	 */
	protected function init() {
		$this->log->debug("Using DatabaseSessionHandler");
		
		$this->tableName = dsConfig::read("session.database.tablename", "ds_session");#

		$dsn = dsConfig::read('session.database.dsn');
		$user = dsConfig::read('session.database.user');
		$passwd = dsConfig::read('session.database.passwd');

		if (dsStringTools::isNullOrEmpty($dsn) || dsStringTools::isNullOrEmpty($user) || dsStringTools::isNullOrEmpty($passwd)) {
			throw new dsSessionException(dsSessionException::DB_CONNECTION_MISSING);
		}

		$this->pdo = new PDO($dsn, $user, $passwd);
		$this->checkTable();
	}

	/**
	 * Check if the session table exists and create it if not.
	 */
	private function checkTable() {
		$this->log->debug("checking if table " . $this->tableName . " exists");
		$sql = "SHOW TABLES LIKE :TABLE";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindParam(':TABLE', $this->tableName);

		$tableExist = FALSE;
		if ($stmt->rowCount() > 0) {
			$tableExist = TRUE;
		}

		if (!$tableExist) {
			$this->log->debug("creating table " . $this->tableName . " exists");
			$sql = "CREATE TABLE IF NOT EXISTS  `" . $this->tableName. "` (
					  `session_id` varchar(32) NOT NULL default '',
					  `user_agent` varchar(255) NOT NULL default '',
					  `session_expire` datetime NOT NULL,
					  `date_created` datetime NOT NULL,
					  `session_data` longtext,
					  PRIMARY KEY  (`session_id`),
					  KEY `session_expire` (`session_expire`)
					) ENGINE=MyISAM";

			$this->pdo->exec($sql);
		}
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
		$userAgent = dsApp::getRequest()->getHeader("http_user_agent");

		// create a query to get the session data
		$sql = "SELECT * FROM " . $this->tableName . " WHERE session_id = :SESSION_ID
					AND user_agent = :USER_AGENT AND session_expire > :TIME";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindParam(':SESSION_ID', $sessionId);
		$stmt->bindParam(':USER_AGENT', $userAgent);
		$stmt->bindParam(':TIME', time());
		$result = $stmt->execute();

		// fetch result if exists
		$result = '';
		if ($stmt->rowCount() > 0) {
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$result = $row["session_data"];
		}

		return $result;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\session.dsASessionHandler::write()
	 */
	public function write($sessionId, $data) {
		$userAgent = dsApp::getRequest()->getHeader("http_user_agent");

		// check if some data was given
		if ($data == NULL) {
			return TRUE;
		}

		// update current session value
		$updateSql = 'UPDATE ' . $this->tableName . ' SET session_expire = FROM_UNIXTIME(:SESSION_EXPIRE), session_data = :SESSION_DATA
					WHERE session_id = :SESSION_ID AND user_agent = :USER_AGENT';

		$updateStmt = $this->pdo->prepare($updateSql);
		$updateStmt->bindParam(':SESSION_EXPIRE', time());
		$updateStmt->bindParam(':SESSION_DATA', $data);
		$updateStmt->bindParam(':SESSION_ID', $sessionId);
		$updateStmt->bindParam(':USER_AGENT', $userAgent);
		$updateResult = $updateStmt->execute();

		// current session was updated
		if ($updateStmt->rowCount() > 0) {
			return TRUE;
		}

		// session does not exists create insert statement
		$insertSQL = 'INSERT INTO ' . $this->tableName . ' (session_id, user_agent, session_expire, date_created, session_data)
					VALUES (:SESSION_ID, :USER_AGENT, FROM_UNIXTIME(:SESSION_EXPIRE), FROM_UNIXTIME(:DATE_CREATED), :SESSION_DATA)';

		$insertStmt = $this->pdo->prepare($insertSQL);
		$insertStmt->bindParam(':SESSION_ID', $sessionId);
		$insertStmt->bindParam(':USER_AGENT', $userAgent);
		$insertStmt->bindParam(':SESSION_EXPIRE', time());
		$insertStmt->bindParam(':DATE_CREATED', time());
		$insertStmt->bindParam(':SESSION_DATA', $data);
		$insertResult = $insertStmt->execute();

		return $insertResult;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\session.dsASessionHandler::destroy()
	 */
	public function destroy($sessionId) {
		// create a query to delete a session
		$sql = 'DELETE FROM ' . $this->tableName . ' WHERE session_id = :SESSION_ID';

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindParam(':SESSION_ID', sessionId);
		$result = $stmt->execute();

		return $result;
	}

	/*
	 * (non-PHPdoc)
	 * @see DevelSuite\session.dsASessionHandler::gc()
	 */
	public function gc($lifetime) {
		/* period after that a session pass off */
		$lifetime = time() - $this->sessionLifetime;
			
		// delete statement
		$sql = 'DELETE FROM ' . $this->tableName . ' WHERE session_expire < FROM_UNIXTIME(:LIFETIME)';

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':LIFETIME', $lifetime, PDO::PARAM_STR);
		$result = $stmt->execute();

		return $result;
	}
}