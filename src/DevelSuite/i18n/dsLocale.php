<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\i18n;

// predefined locales
dsLocale::$UNITED_KINGDOM = new dsLocale("en", "GB");
dsLocale::$FRANCE = new dsLocale("fr", "FR");
dsLocale::$GERMANY = new dsLocale("de", "DE");
dsLocale::$ITALY = new dsLocale("it", "IT");
dsLocale::$SPAIN = new dsLocale("es", "ES");

/**
 * Defining a Locale with country code and language token
 *
 * @package DevelSuite\i18n
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsLocale {
	// predefined locales
	public static $UNITED_KINGDOM;
	public static $FRANCE;
	public static $GERMANY;
	public static $ITALY;
	public static $SPAIN;

	/**
	 * Language of the locale
	 * @var string
	 */
	private $language;

	/**
	 * Country code of the locale
	 * @var string
	 */
	private $country;

	/**
	 * Constructor
	 *
	 * @param string $language
	 * 		Language token of the locale
	 * @param string $country
	 * 		Country code of the locale
	 */
	public function __construct($language, $country) {
		$this->language = $language;
		$this->country = $country;
	}

	/**
	 * Returns the language token of the locale
	 */
	public function getLanguage() {
		return $this->language;
	}

	/**
	 * Returns the country code of the locale
	 */
	public function getCountry() {
		return $this->country;
	}
}