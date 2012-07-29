<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\view\helper\impl;

use DevelSuite\view\helper\dsIViewHelper;

/**
 * ViewHelper to handle BBCode parsing.
 *
 * @package DevelSuite\view\helper\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsDateViewHelper implements dsIViewHelper {
	# array with all BBCodes to search for
	private static $searchBBCodes = array(
											'/\[u\](.+?)\[\/u\]/i', 
											'/\[i\](.+?)\[\/i\]/i', 
											'/\[b\](.+?)\[\/b\]/i', 
											'/\[s\](.+?)\[\/s\]/i', 
											'/\[url\](.+?)\[\/url\]/i', 
											'/\[url=(.+?)\](.+?)\[\/url\]/i', 
											'/\[quote\](.+?)\[\/quote\]/is',
											'/\[size=big\](.+?)\[\/size\]/i', 
											'/\[size=small\](.+?)\[\/size\]/i', 
											'/\[color=([[:alnum:]]{6}?)\](.*?)\[\/color\]/i');

	# array with all XHTML-compliant replacecements for the BBCode
	private static $BBCodeXHTMLReplacements = array(
											'<span style="text-decoration: underline">\\1</span>', 
											'<em>\\1</em>', 
											'<strong>\\1</strong>', 
											'<strike>\\1</strike>', 
											'<a href="\\1" title="\\1">\\1</a>',
											'<a href="\\1" title="\\2">\\2</a>', 
											'<br/>Zitat:<br/><div class="gb_quote">\\1</div><br/>', 
											'<span style="font-size: 1.5em">\\1</span>', 
											'<span style="font-size: 0.5em">\\1</span>',
											'<span style="color: \\1">\\2</span>');

	/**
	 * Parses the BBCode of a string
	 *
	 * @param string $string
	 * 		String to be parsed
	 * @param array $possibleSmilieCodes
	 * 		Array of all allowed smilie codes
	 * @param string $smiliePath
	 * 		String with the path to the images
	 */
	public function parse($string, $allowedSmilieCodes = array(), $smiliePath = "") {
		$string = preg_replace(self::$searchBBCodes, self::$BBCodeXHTMLReplacements, $string);
		if(count($allowedSmilieCodes) > 0) {
			$string = $this->parseSmilies($string, $allowedSmilieCodes, $smiliePath);
		}

		return $string;
	}

	/**
	 * If smilies are allowed in BBCode, the smilie codes will be replaced with the path to the image
	 *
	 * @param string $string
	 * 		The string to be parsed
	 */
	private function parseSmilies($string, array $allowedSmilieCodes, $smiliePath){
		$html = $string;

		foreach($allowedSmilieCodes as $smilieCode) {
			if(preg_match("/\:$smilieCode\:/i", $html)) {
				$html = preg_replace("/\:$smilieCode\:/i", "<img src='" . $smiliePath. "/" . $smilieCode . ".gif' border='0' width='20' height='20' alt='" . $smilieCode . "' />", $html);
			}
		}

		return $html;
	}
}