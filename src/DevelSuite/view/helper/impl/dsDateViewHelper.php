<?php
/*
 * This file is part of the DevelSuite
* Copyright (C) 2012 Georg Henkel <info@develman.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
namespace DevelSuite\view\helper\impl;

use DevelSuite\i18n\dsLocale;

use DevelSuite\config\dsConfig;

use DevelSuite\dsApp;

use DevelSuite\view\helper\dsIViewHelper;

/**
 * ViewHelper to handle date operations.
 *
 * @package DevelSuite\view\helper\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsDateViewHelper implements dsIViewHelper {
	/**
	 * Static definitions of time values for defined languages
	 */
	private static $lang_en = array("d" => "days", "h" => "hours", "m" => "minutes", "s" => "seconds");
	private static $lang_de = array("d" => "Tage", "h" => "Stunden", "m" => "Minuten", "s" => "Sekunden");
	private static $lang_fr = array("d" => "jours", "h" => "heures", "m" => "minutes", "s" => "secondes");
	private static $lang_es = array("d" => "días", "h" => "horas", "m" => "minutos", "s" => "segundos");
	private static $lang_it = array("d" => "giorni", "h" => "orario", "m" => "minuti", "s" => "secondo");

	/**
	 * Contains definition of time values
	 * @var array
	 */
	private $lang;

	/**
	 * Constructor
	 */
	public function __construct() {
		$locale = dsConfig::read('app.locale', dsLocale::$UNITED_KINGDOM);
		switch ($locale) {
			case dsLocale::$UNITED_KINGDOM:
				$lang = self::$lang_en;
				break;

			case dsLocale::$GERMANY:
				$lang = self::$lang_de;
				break;

			case dsLocale::$FRANCE:
				$lang = self::$lang_fr;
				break;
					
			case dsLocale::$SPAIN:
				$lang = self::$lang_es;
				break;

			case dsLocale::$ITALY:
				$lang = self::$lang_it;
				break;
		}
	}

	/**
	 * Calculate difference off a datetime to current time
	 * and format it in words (e.g. 1 hour, 2 days, ...)
	 *
	 * @param string $time
	 */
	public function diffTime($time, array $lang = NULL) {
		if ($lang != NULL) {
			$this->lang = $lang;
		}

		$todaydate = date("Y-m-d H:i:s");

		$ago = strtotime($todaydate) - strtotime($time);
		if ($ago >= 86400) {
			$diff = floor($ago/86400) . ' ' . $this->lang['d'];
		} elseif ($ago >= 3600) {
			$diff = floor($ago/3600) . ' ' . $this->lang['h'];
		} elseif ($ago >= 60) {
			$diff = floor($ago/60) . ' ' . $this->lang['m'];
		} else {
			$diff = $ago . '  ' . $this->lang['s'];
		}

		return $diff;
	}
}
