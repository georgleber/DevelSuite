<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\i18n;

use DevelSuite\config\dsConfig;
use DevelSuite\exception\spl\dsFileNotFoundException;
use DevelSuite\util\dsStringTools;

// default locale for all bundles
dsResourceBundle::$DEFAULT_LOCALE = dsLocale::$UNITED_KINGDOM;

/**
 * Loads message bundles depending on the configured locale
 *
 * @package DevelSuite\i18n
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsResourceBundle {
	// default locale for bundle
	public static $DEFAULT_LOCALE;

	/**
	 * Loads a message bundle for the defined locale
	 *
	 * @param string $filePath
	 * 		Path to the bundle
	 * @param string $bundleName
	 * 		Name of the Bundle
	 * @throws dsFileNotFoundException
	 */
	public static function getBundle($filePath, $bundleName) {
		$postfix = "";

		// load locale postfix
		$locale = dsConfig::read('app.locale');
		if(isset($locale) && $locale !== self::$DEFAULT_LOCALE) {
			$postfix = $locale->getLanguage();
		}

		$bundleFile = "";
		if(dsStringTools::isFilled($postfix)) {
			$bundleFile = $filePath . DS . $bundleName . "_" . $postfix . ".ini";
		} else {
			$bundleFile = $filePath . DS . $bundleName . ".ini";
		}

		if (!isset($bundleFile) || !file_exists($bundleFile)) {
			throw new dsFileNotFoundException("Bundle cannot be loaded: " . $bundleFile);
		}

		return parse_ini_file($bundleFile);
	}
}