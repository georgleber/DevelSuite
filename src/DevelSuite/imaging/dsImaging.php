<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\imaging;

/**
 * FIXME
 *
 * @package DevelSuite\imaging
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsImaging {
	const TYPE_JPEG = "JPEG";
	const TYPE_GIF  = "GIF";
	const TYPE_PNG 	= "PNG";

	private $image;
	private $type;
	private $imagePath;

	//Creates a new class
	public function __construct($imagePath = '') {
		$this->image = NULL;
		$this->imagePath = $imagePath;
		if($imagePath != '') {
			$this->loadByPath($imagePath);
		}
	}

	//get the type of an image: supports only png, jpg and gif.
	//$path is the location of the image.
	public function getType($path) {
		$a = getimagesize($path);

		switch($a[2]) {
			case 3:
				return 'png';
				break;

			case 2:
				return 'jpg';
				break;

			case 1:
				return 'gif';
		}

		return FALSE;
	}

	//set the image from a resource.
	public function loadByResource($resource) {
		$this->image = $resource;
	}

	//Sets the image from a path.
	public function loadByPath($imagePath) {
		if($imagePath) {
			$a = getimagesize($imagePath);

			switch($a[2]) {
				case 3:
					$this->image = imagecreatefrompng($imagePath);
					$this->type = self::TYPE_PNG;
					break;

				case 2:
					$this->image =imagecreatefromjpeg($imagePath);
					$this->type = self::TYPE_JPEG;
					break;

				default:
					$this->image =imagecreatefromgif($imagePath);
					$this->type = self::TYPE_GIF;
			}
		}
	}

	private function getWidth() {
		return imagesx($this->image);
	}

	private function getHeight() {
		return imagesy($this->image);
	}

	//Tag the image in the bottom right corner with another image
	public function tag($imagePath, $x = -1, $y = -1) {
		$image = new rgImage();
		$image->loadByPath($imagePath);

		$x = $this->getWidth() - $image->getWidth();
		$y = $this->getHeight() - $image->getHeight();

		imagecopyresized ($this->image, $image->image, $x, $y, 0, 0, $this->getWidth(), $this->getHeight(), $this->getWidth(), $this->getHeight());
	}

	//Output or save the image
	public function gif($imagePath = NULL) {
		$result = FALSE;
		if($imagePath) {
			$h = fopen($imagePath, 'w');
			fclose($h);
			$result = imagegif($this->image, $imagePath);
			chmod($imagePath, 0777);
		} else {
			$result = imagegif($this->image);
		}

		imagedestroy($this->image);
		$this->image = NULL;
		
		return $result;
	}

	//Output or save the image
	public function png($imagePath = NULL) {
		$result = FALSE;
		if($imagePath) {
			$h = fopen($imagePath, 'w');
			fclose($h);
			$result = imagepng($this->image, $imagePath);
			chmod($imagePath, 0777);
		} else {
			$result = imagepng($this->image);
		}

		imagedestroy($this->image);
		$this->image = NULL;
		
		return $result;
	}

	//Output or save the image
	public function jpeg($imagePath = NULL) {
		$result = FALSE;
		if($imagePath) {
			$h = fopen($imagePath, 'w');
			fclose($h);
			$result = imagejpeg($this->image, $imagePath, 100);
			chmod($imagePath, 0777);
		} else {
			$result = imagejpeg($this->image);
		}
		
		imagedestroy($this->image);
		$this->image = NULL;

		return $result;
	}

	public function jpg($imagePath = NULL) {
		return $this->jpeg($imagePath);
	}

	//Return a copy of the image
	public function copy() {
		$image = imagecreatetruecolor($this->getWidth(), $this->getHeight());
		imagecopy ($image, $this->image, 0, 0, 0, 0, $this->getWidth(), $this->getHeight());
		$image1 = new rgImage();
		$image1->loadByResource($image);
		return $image1;
	}

	//Resize the image
	public function resize($width, $height = NULL) {
		$proportion = $width / $this->getWidth();
		if($height == NULL) {
			$height = round(($proportion * $this->getHeight()));
		}

		$image = imagecreatetruecolor($width, $height);
		imagecopyresampled ($image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		$this->image = $image;
	}

	public function resizeAuto($maxWidth, $maxHeight) {
		$ratio = $this->getWidth() / $this->getHeight();

		if($maxWidth / $maxHeight > $ratio) {
			$maxWidth = $maxHeight * $ratio;
		} else {
			$maxHeight = $maxWidth / $ratio;
		}

		$this->resize($maxWidth, $maxHeight);
	}

	public function resizeToHeight($height) {
		$ratio = $height / $this->getHeight();
		$width = $this->getWidth() * $ratio;
		$this->resize($width, $height);
	}

	public function resizeToWidth($width) {
		$ratio = $width / $this->getWidth();
		$height = $this->getHeight() * $ratio;
		$this->resize($width, $height);
	}

	public function minimizeToWidth($width) {
		if($this->getWidth() > $width) {
			$this->resizeToWidth($width);
		}
	}

	public function __destruct() {
		if ($this->image != NULL) {
			imagedestroy($this->image);
		}
	}
}