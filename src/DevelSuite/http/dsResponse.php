<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\http;

use DevelSuite\config\dsConfig;

/**
 * FIXME
 *
 * @package DevelSuite\http
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsResponse {
	private $protocol = "HTTP/1.1";
	private $statusCode = "200";
	private $statusText = "OK";
	private $contentType = "text/html";
	private $charset = "UTF-8";

	private $headers = array();
	private $content;
	private $cookies = array();
	private $headersOnly;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->protocol = dsConfig::read("app.http.protocol", "HTTP/1.1");
		$this->charset = dsConfig::read("app.http.charset", "UTF-8");
	}

	/**
	 * Holds HTTP response statuses
	 *
	 * @var array
	 */
	protected $_statusCodes = array(
	100 => 'Continue',
	101 => 'Switching Protocols',
	200 => 'OK',
	201 => 'Created',
	202 => 'Accepted',
	203 => 'Non-Authoritative Information',
	204 => 'No Content',
	205 => 'Reset Content',
	206 => 'Partial Content',
	300 => 'Multiple Choices',
	301 => 'Moved Permanently',
	302 => 'Found',
	303 => 'See Other',
	304 => 'Not Modified',
	305 => 'Use Proxy',
	307 => 'Temporary Redirect',
	400 => 'Bad Request',
	401 => 'Unauthorized',
	402 => 'Payment Required',
	403 => 'Forbidden',
	404 => 'Not Found',
	405 => 'Method Not Allowed',
	406 => 'Not Acceptable',
	407 => 'Proxy Authentication Required',
	408 => 'Request Time-out',
	409 => 'Conflict',
	410 => 'Gone',
	411 => 'Length Required',
	412 => 'Precondition Failed',
	413 => 'Request Entity Too Large',
	414 => 'Request-URI Too Large',
	415 => 'Unsupported Media Type',
	416 => 'Requested range not satisfiable',
	417 => 'Expectation Failed',
	500 => 'Internal Server Error',
	501 => 'Not Implemented',
	502 => 'Bad Gateway',
	503 => 'Service Unavailable',
	504 => 'Gateway Time-out'
	);

	public function setStatusCode($code, $text) {
		$this->statusCode = $code;
		if(NULL !== $text) {
			$this->statusText = $text;
		} else {
			$this->statusText = $statusTexts[$code];
		}
	}

	public function setHeadersOnly($headersOnly) {
		$this->headersOnly = $headersOnly;
	}

	public function headersOnly() {
		return $this->headersOnly;
	}

	public function setContentType($contentType) {
		$this->contentType = $contentType;
	}

	public function getContentType() {
		return $this->contentType;
	}

	/**
	 * Sets a header.
	 *
	 * @param string  $name     header name
	 * @param string  $value    Value (if null, remove the header)
	 * @param bool    $replace  Replace for the value
	 *
	 */
	public function addHeader($name, $value = NULL) {
		if (!isset($value)) {
			$this->headers[$name] = NULL;
		} else {
			$this->headers[$name] = $value;
		}
	}

	/**
	 * Checks if response has given HTTP header.
	 *
	 * @param  string $name  HTTP header name
	 *
	 * @return bool
	 */
	public function hasHeader($name) {
		return array_key_exists($name, $this->headers);
	}

	/**
	 * Add content of the page(s)
	 *
	 * @param $content
	 * 			content displaying the page
	 */
	public function setContent($content) {
		$this->content = $content;
	}

	/**
	 * @return the content
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * Replace current content with a new one
	 *
	 * @param string $newContent
	 * 			replacing content
	 */
	public function replaceContent($newContent) {
		$this->content = $newContent;
	}

	/**
	 * Sends HTTP headers and cookies. Only the first invocation of this method will send the headers.
	 * Subsequent invocations will silently do nothing. This allows certain actions to send headers early,
	 * while still using the standard controller.
	 */
	public function sendHeaders()
	{
		// status-line
		$status = "{$this->protocol} {$this->statusCode} {$this->statusText}";
		header($status);

		$contentType = "Content-Type: " . "{$this->contentType}; charset={$this->charset}";
		header($contentType);

		foreach ($this->headers as $name => $value) {
			if($value === NULL) {
				header("{$name}");
			} else {
				header("{$name}: {$value}");
			}
		}

		// cookies
		foreach ($this->cookies as $cookie) {
			setcookie($cookie['name'], $cookie['value'], $cookie['expire'], $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httpOnly']);
		}
	}

	/**
	 * Sends the HTTP headers and the content.
	 */
	public function send() {
		$this->sendHeaders();

		if(!$this->headersOnly) {
			$this->content = ob_get_clean();
			echo $this->content;
		}
	}

	/**
	 * Redirects to another url. If immediately is TRUE it redirects
	 * directly without finishing the script.
	 *
	 * @param string $url
	 * 		url to which will redirected
	 * @param bool $immediately
	 * 		redirect immediately?
	 */
	public function redirectURL($url, $immediately = FALSE) {
		$this->addHeader("Location", $url);

		if($immediately === TRUE) {
			$this->setHeadersOnly(TRUE);
			$this->send();
			exit();
		}
	}

	/**
	 * Sets the correct headers to instruct the client to not cache the response
	 */
	public function disableCache() {
		$this->addHeader('Expires', "Mon, 26 Jul 1997 05:00:00 GMT");
		$this->addHeader('Last-Modified', "Mon, 26 Jul 1997 05:00:00 GMT");
		$this->addHeader('Cache-Control', "no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
		$this->addHeader('Pragma', "no-cache");
	}

	/**
	 * Sets the correct headers to instruct the client to cache the response.
	 *
	 * @param string $since
	 * 				A valid time since the response text has not been modified
	 * @param string $time
	 * 				A valid time for cache expiry
	 */
	public function cache($since, $time = '+1 day') {
		if (!is_integer($time)) {
			$time = strtotime($time);
		}

		$this->addHeader('Date', gmdate("D, j M Y G:i:s ", time()) . "GMT");
		$this->addHeader('Last-Modified', gmdate("D, j M Y G:i:s ", $since) . "GMT");
		$this->addHeader('Expires', gmdate("D, j M Y H:i:s", $time) . " GMT");
		$this->addHeader('Cache-Control', "public, max-age=" . ($time - time()));
		$this->addHeader('Pragma', "cache");
	}

	/**
	 * Sets a cookie
	 *
	 * @param string $name
	 * 				HTTP header name
	 * @param string $value
	 * 				Value for the cookie
	 * @param string $expire
	 * 				Cookie expiration period
	 * @param string $path
	 * 				Path
	 * @param string $domain
	 * 				Domain name
	 * @param bool $secure
	 * 				If secure
	 * @param bool $httpOnly
	 * 				If uses only HTTP
	 */
	public function setCookie($name, $value, $expire = NULL, $path = '/', $domain = '', $secure = FALSE, $httpOnly = FALSE) {
		if ($expire !== NULL) {
			if (is_numeric($expire)) {
				$expire = (int) $expire;
			} else {
				$expire = strtotime($expire);
				if ($expire === FALSE || $expire == -1) {
					throw new dsResponseException('Your expire parameter is not valid.');
				}
			}
		}

		$this->cookies[$name] = array(
							      'name'     => $name,
							      'value'    => $value,
							      'expire'   => $expire,
							      'path'     => $path,
							      'domain'   => $domain,
							      'secure'   => $secure ? true : false,
							      'httpOnly' => $httpOnly);
	}

	/**
	 * Deletes a cookie
	 *
	 * @param string $name
	 * 			Name of the cookie
	 */
	public function deleteCookie($name, $path = '/', $domain = '', $secure = FALSE) {
		setCookie($name, "", time() - 3600, $path, $domain, $secure);
		unset($this->cookies[$name]);
	}

}