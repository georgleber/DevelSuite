<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2011 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\i18n;

use DevelSuite\config\dsConfig;
use DevelSuite\exception\spl\dsFileNotFoundException;

dsResourceBundle::$DEFAULT_LOCALE = dsLocale::$UNITED_KINGDOM;

/**
 * FIXME
 *
 * @package DevelSuite\i18n
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsResourceBundle {
	public static $DEFAULT_LOCALE;

	public static function getBundle($filePath) {
		$postfix = "";

		$locale = dsConfig::read('app.locale');
		if(isset($locale) && $locale !== self::$DEFAULT_LOCALE) {
			$postfix = $locale->getLanguage();
		}

		$file = "";
		if($postfix != "") {
			$file = $filePath."_".$postfix.".ini";
		} else {
			$file = $filePath.".ini";
		}

		if (!isset($file) || !file_exists($file)) {
			throw new dsFileNotFoundException("File can not be loaded: ".$file);
		}

		$iniFile = parse_ini_file($file, TRUE);
		return $iniFile;
	}
}