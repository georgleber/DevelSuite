<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2011 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\util;

/**
 * FIXME
 *
 * @package DevelSuite\util
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsStringTools {
	public static function isNullOrEmpty($string) {
		# FIXME: check if $string is a string
		$string = trim($string);
		if(empty($string) && !is_numeric($string)) {
			return TRUE;
		}

		return FALSE;
	}

	public static function isFilled($string) {
		return self::isNullOrEmpty($string) == FALSE;
	}

	public static function sanitizeMagicQuotes(&$value) {
		if(function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc()) {
			$sybaseOn = FALSE;
			if(ini_get('magic_quotes_sybase') && (strtolower(ini_get('magic_quotes_sybase')) != "off")) {
				$sybaseOn = TRUE;
			}
				
			$value = self::stripslashesDeep($value, $sybaseOn);
		}

		return $value;
	}

	public static function stripslashesDeep(&$value, $sybaseOn) {
		if(is_array($value)) {
			foreach ($value as $val) {
				self::stripslashesDeep($val, $sybaseOn);
			}
		} else {
			if($sybaseOn) {
				$value = str_replace("\'\'","\'", $value);
			} else {
				$value = stripslashes($value);
			}
		}

		return $value;
	}

	public function removeInvisibleChars($str, $url_encoded = TRUE)
	{
		$non_displayables = array();

		// every control character except newline (dec 10)
		// carriage return (dec 13), and horizontal tab (dec 09)

		if ($url_encoded)
		{
			$non_displayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
			$non_displayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
		}

		$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

		do
		{
			$str = preg_replace($non_displayables, '', $str, -1, $count);
		}
		while ($count);

		return $str;
	}

	public static function replaceUmlauts($str) {
		$umlauts = Array("/ä/","/ö/","/ü/","/Ä/","/Ö/","/Ü/","/ß/");
		$replace = Array("ae","oe","ue","Ae","Oe","Ue","ss");

		$str = preg_replace($umlauts, $replace, $str);
			
		return $str;
	}

	public static function truncate($text, $length, $suffix = '&hellip;', $isHTML = TRUE) {
		$i = 0;
		$simpleTags = array('br' => TRUE, 'hr' => TRUE, 'input' => TRUE, 'image' => TRUE, 'link' => TRUE, 'meta' => TRUE);
		$tags = array();
		if ($isHTML) {
			preg_match_all('/<[^>]+>([^<]*)/', $text, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
			foreach ($matches as $match) {
				if ($match[0][1] - $i >= $length) {
					break;
				}

				$t = substr(strtok($match[0][0], " \t\n\r\0\x0B>"), 1);
				// test if the tag is unpaired, then we mustn't save them
				if($t[0] != '/' && (!isset($simpleTags[$t]))) {
					$tags[] = $t;
				} elseif (end($tags) == substr($t, 1)) {
					array_pop($tags);
				}

				$i += $match[1][1] - $match[0][1];
			}
		}

		// output without closing tags
		$output = substr($text, 0, $length = min(strlen($text),  $length + $i));
		// closing tags
		$output2 = (count($tags = array_reverse($tags)) ? '</' . implode('></', $tags) . '>' : '');

		// Find last space or HTML tag (solving problem with last space in HTML tag eg. <span class="new">)
		$pos = (int)end(end(preg_split('/<.*>| /', $output, -1, PREG_SPLIT_OFFSET_CAPTURE)));
		// Append closing tags to output
		$output .= $output2;

		// Get everything until last space
		$one = substr($output, 0, $pos);
		// Get the rest
		$two = substr($output, $pos, (strlen($output) - $pos));
		// Extract all tags from the last bit
		preg_match_all('/<(.*?)>/s', $two, $tags);
		// Add suffix if needed
		if (strlen($text) > $length) { $one .= $suffix; }
		// Re-attach tags
		$output = $one . implode($tags[0]);

		return $output;
	}
}