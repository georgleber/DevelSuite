<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\form\element\validator;

/**
 * FIXME
 *
 * @package DevelSuite\form\element\validator
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsMimeTypes {
	public static $PNG = array("png" => array("image/png"));
	public static $JPG = array("jpg" => array("image/jpg", "image/jpeg", "image/pjpeg"));
	public static $GIF = array("gif" => array("image/gif"));
	public static $BMP = array("bmp" => array("image/bmp"));
	public static $DOC = array("doc" => array("application/msword"));
	public static $PDF = array("pdf" => array("application/pdf"));
}