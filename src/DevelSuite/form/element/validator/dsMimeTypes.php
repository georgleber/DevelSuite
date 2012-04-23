<?php
namespace DevelSuite\core\form\element\validator;

class dsMimeTypes {
	public static $PNG = array("png" => array("image/png"));
	public static $JPG = array("jpg" => array("image/jpg", "image/jpeg", "image/pjpeg"));
	public static $GIF = array("gif" => array("image/gif"));
	public static $BMP = array("bmp" => array("image/bmp"));
	public static $DOC = array("doc" => array("application/msword"));
	public static $PDF = array("pdf" => array("application/pdf"));
}