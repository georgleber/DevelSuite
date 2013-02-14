<?php
namespace DevelSuite\error;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class dsErrorHandler {
	public static function handleError($level, $message, $file, $line, $context) {
		echo "TEST";
		$log = new Logger("ErrorHandler");
		$log->pushHandler(new StreamHandler(LOG_PATH . DS . 'error.log'));

		switch ($level) {
			case E_USER_ERROR:
				$log->err("Fatal: [" . $level . "] " . $message . " in file: " . $file . " in line: " . $line);
				break;

			case E_USER_WARNING:
				$log->warn("Warning: [" . $level . "] " . $message . " in file: " . $file . " in line: " . $line);
				break;

			case E_USER_NOTICE:
				$log->info("Notice: [" . $level . "] " . $message . " in file: " . $file . " in line: " . $line);
				break;

			default:
				$log->alert("Unknown error: [" . $level . "] " . $message . " in file: " . $file . " in line: " . $line);
				break;
		}
	}
}