<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\mail;

use DevelSuite\config\dsConfig;

use \Swift_Message as Swift_Message;
use \Swift_Transport as Swift_Transport;
use \Swift_Mailer as Swift_Mailer;
use \Swift_SmtpTransport as Swift_SmtpTransport;
use \Swift_SendmailTransport as Swift_SendmailTransport;
use \Swift_MailTransport as Swift_MailTransport;

/**
 * FIXME
 *
 * @package DevelSuite\mail
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsMailer {
	private $message;
	private $transport;

	public function __construct() {
		$this->loadTransport();
	}

	public function setTransport(Swift_Transport $transport) {
		$this->transport = $transport;
	}

	public function setMessage(Swift_Message $message) {
		$from = $message->getFrom();
		$sender = dsConfig::read('mailing.default.sender');
		
		if (empty($from) && !empty($sender)) {
			$message->setFrom($sender);
		}
		$this->message = $message;
	}

	public function send() {
		$mailer = new Swift_Mailer($this->transport);
		$mailer->send($this->message);
	}

	private function loadTransport() {
		$transpType = dsConfig::read('mailing.transport');
		switch($transpType) {
			case 'smtp':
				{
					$mailCfg = dsConfig::read('mailing.smtp');
					$hostname = $mailCfg['hostname'];
					$port = 25;
					$username = $mailCfg['username'];
					$password = $mailCfg['password'];

					if (isset($mailCfg['port'])) {
						$port = $mailCfg['port'];
					}

					$this->transport = Swift_SmtpTransport::newInstance($hostname, $port);
					if (isset($username) && isset($password)) {
						$this->transport->setUsername($username);
						$this->transport->setPassword($password);
					}

					if (isset($mailCfg['useTLS']) && $mailCfg['useTLS'] == TRUE) {
						$this->transport->setEncryption('tls');
					}
					break;
				}

			case 'sendmail':
				{
					$mailCfg = dsConfig::read('mailing.sendmail');
					$path = $mailCfg['path'];

					if (!isset($path)) {
						$path = "/usr/sbin/sendmail -bs";
					}

					$this->transport = Swift_SendmailTransport::newInstance($path);
					break;
				}

			case 'mail':
				{
					$this->transport = Swift_MailTransport::newInstance();
					break;
				}
		}
	}
}