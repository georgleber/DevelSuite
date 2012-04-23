<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2011 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\i18n;

dsLocale::$UNITED_KINGDOM = new dsLocale("en", "GB");
dsLocale::$FRANCE = new dsLocale("fr", "FR");
dsLocale::$GERMANY = new dsLocale("de", "DE");
dsLocale::$ITALY = new dsLocale("it", "IT");
dsLocale::$SPAIN = new dsLocale("es", "ES");

/**
 * FIXME
 *
 * @package DevelSuite\i18n
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsLocale {
	public static $UNITED_KINGDOM;
	public static $FRANCE;
	public static $GERMANY;
	public static $ITALY;
	public static $SPAIN;

	private $language;
	private $country;

	public function __construct($language, $country) {
		$this->language = $language;
		$this->country = $country;
	}

	public function getLanguage() {
		return $this->language;
	}

	public function getCountry() {
		return $this->country;
	}
}