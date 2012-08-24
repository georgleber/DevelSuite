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
 * This class can be used for manipulating images (resize / crop)
 *
 * @package DevelSuite\imaging
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsImaging {
	const OPTION_AUTO 		= 100;
	const OPTION_EXACT		= 200;
	const OPTION_PORTRAIT 	= 300;
	const OPTION_LANDSCAPE 	= 400;
	const OPTION_CROP 		= 500;

	/**
	 * The image to resize
	 * @var resource
	 */
	private $image;

	/**
	 * Width of the image
	 * @var double
	 */
	private $width;

	/**
	 * height of the image
	 * @var double
	 */
	private $height;

	/**
	 * The resiezd image
	 * @var resource
	 */
	private $imageResized;

	/**
	 * Constructor
	 *
	 * @param string $fileName
	 * 		Name of the image file
	 */
	public function __construct($fileName) {
		// open the image
		$this->image = $this->openImage($fileName);

		$this->width= imagesx($this->image);
		$this->height= imagesy($this->image);
	}

	/**
	 * Resize the image to the new dimensions
	 *
	 * @param double $newWidth
	 * 		New width of the image
	 * @param double $newHeight
	 * 		New height of the image
	 * @param int $option
 	 * 		Type of resizing
	 * 		
	 */
	public function resizeImage($newWidth, $newHeight, $option = self::OPTION_AUTO) {
		// Get optimal width and height - based on $option
		$optionArray = $this->calculateDimensions($newWidth, $newHeight, $option);

		$optimalWidth  = $optionArray['optimalWidth'];
		$optimalHeight = $optionArray['optimalHeight'];

		// *** Resample - create image canvas of x, y size
		$this->imageResized = imagecreatetruecolor($optimalWidth, $optimalHeight);
		imagecopyresampled($this->imageResized, $this->image, 0, 0, 0, 0, $optimalWidth, $optimalHeight, $this->width, $this->height);

		// *** if option is 'crop', then crop too
		if ($option == self::OPTION_CROP) {
			$this->crop($optimalWidth, $optimalHeight, $newWidth, $newHeight);
		}
	}

	/**
	 * Save the image
	 *
	 * @param string $savePath
	 * 		Path to save the image to
	 * @param string $imageQuality
	 * 		Quality of the image
	 */
	public function saveImage($savePath, $imageQuality = "100") {
		// Get extension
		$size = getimagesize($savePath);
		$extension = $size["mime"];

		if($extension === "image/jpeg") {
			if (imagetypes() & IMG_JPG) {
				imagejpeg($this->imageResized, $savePath, $imageQuality);
			}
		} else if ($extension === "image/gif") {
			if (imagetypes() & IMG_GIF) {
				imagegif($this->imageResized, $savePath);
			}
		} else if ($extension === "image/png") {
			// Scale quality from 0-100 to 0-9 and invert it (0 is best)
			$scaleQuality = round(($imageQuality / 100) * 9);
			$invertScaleQuality = 9 - $scaleQuality;

			if (imagetypes() & IMG_PNG) {
				imagepng($this->imageResized, $savePath, $invertScaleQuality);
			}
		}

		imagedestroy($this->imageResized);
	}

	/**
	 * Depending on mime type of the image file try to open it
	 *
	 * @param string $imageName
	 * 		Filename of the image
	 */
	private function openImage($imageName) {
		// Get extension
		$size = getimagesize($imageName);
		$extension = $size["mime"];

		$image = NULL;
		// depending on extension load image
		if ($extension === "image/jpeg") {
			$image = @imagecreatefromjpeg($imageName);
		} else if ($extension === "image/gif") {
			$image = @imagecreatefromgif($imageName);
		} else if ($extension === "image/png") {
			$image = @imagecreatefrompng($imageName);
		} else {
			$image = NULL;
		}

		return $image;
	}

	/**
	 * Calculate the optimal sizes depending on the chosen option of the new resizing image
	 *
	 * @param double $width
	 * 		New width of the image
	 * @param double $height
	 * 		New height of the image
	 * @param int $option
	 * 		Type of resizing
	 */
	private function calculateDimensions($newWidth, $newHeight, $option) {
		$optimalWidth = 0;
		$optimalHeight = 0;

		switch ($option) {
			case self::OPTION_AUTO:
				$optionArray = $this->getSizeByAuto($newWidth, $newHeight);
				$optimalWidth = $optionArray['optimalWidth'];
				$optimalHeight = $optionArray['optimalHeight'];
				break;

			case self::OPTION_EXACT:
				$optimalWidth = $newWidth;
				$optimalHeight= $newHeight;
				break;

			case self::OPTION_PORTRAIT:
				$optimalWidth = $this->getSizeByFixedHeight($newHeight);
				$optimalHeight= $newHeight;
				break;

			case self::OPTION_LANDSCAPE:
				$optimalWidth = $newWidth;
				$optimalHeight= $this->getSizeByFixedWidth($newWidth);
				break;
					
			case self::OPTION_CROP:
				$optionArray = $this->getOptimalCrop($newWidth, $newHeight);
				$optimalWidth = $optionArray['optimalWidth'];
				$optimalHeight = $optionArray['optimalHeight'];
				break;
		}

		return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
	}

	/**
	 * Get optimal width by a fixed height
	 *
	 * @param double $height
	 * 		New height of the image
	 */
	private function getSizeByFixedHeight($newHeight) {
		$ratio = $this->width / $this->height;
		$newWidth = $newHeight * $ratio;

		return $newWidth;
	}

	/**
	 * Get optimal height by a fixed width
	 *
	 * @param double $width
	 * 		New width of the image
	 */
	private function getSizeByFixedWidth($newWidth) {
		$ratio = $this->height / $this->width;
		$newHeight = $newWidth * $ratio;

		return $newHeight;
	}

	/**
	 * Check if imageis landscape, protrait or square and calculate optimal resizing ratio depending on format
	 *
	 * @param double $width
	 * 		New width ofthe image
	 * @param double $height
	 * 		New height ofthe image
	 */
	private function getSizeByAuto($newWidth, $newHeight) {
		// image to be resized is wider (landscape)
		if ($this->height < $this->width) {
			$optimalWidth = $newWidth;
			$optimalHeight= $this->getSizeByFixedWidth($newWidth);
		}
		// image to be resized is taller (portrait)
		elseif ($this->height > $this->width) {
			$optimalWidth = $this->getSizeByFixedHeight($newHeight);
			$optimalHeight= $newHeight;
		}
		// image to be resized is a square
		else {
			if ($newHeight < $newWidth) {
				$optimalWidth = $newWidth;
				$optimalHeight= $this->getSizeByFixedWidth($newWidth);
			} else if ($newHeight > $newWidth) {
				$optimalWidth = $this->getSizeByFixedHeight($newHeight);
				$optimalHeight= $newHeight;
			} else {
				// Square being resized to a square
				$optimalWidth = $newWidth;
				$optimalHeight= $newHeight;
			}
		}

		return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
	}

	/**
	 * Calculate optimal ratio when the image will be cropped
	 *
	 * @param double $width
	 * 		New width ofthe image
	 * @param double $height
	 * 		New height ofthe image
	 */
	private function getOptimalCrop($newWidth, $newHeight) {
		$heightRatio = $this->height / $newHeight;
		$widthRatio  = $this->width /  $newWidth;

		if ($heightRatio < $widthRatio) {
			$optimalRatio = $heightRatio;
		} else {
			$optimalRatio = $widthRatio;
		}

		$optimalHeight = $this->height / $optimalRatio;
		$optimalWidth  = $this->width  / $optimalRatio;

		return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
	}

	/**
	 * Crop the image to the new size
	 *
	 * @param double $optimalWidth
	 * 		Optimal new width of the image
	 * @param double $optimalHeight
	 * 		Optimal new height of the image
	 * @param double $newWidth
	 * 		New width of the image
	 * @param double $newHeight
	 * 		New height of the image
	 */
	private function crop($optimalWidth, $optimalHeight, $newWidth, $newHeight) {
		// Find center - this will be used for the crop
		$cropStartX = ($optimalWidth / 2) - ($newWidth / 2);
		$cropStartY = ($optimalHeight / 2) - ($newHeight / 2);

		$crop = $this->imageResized;
		//imagedestroy($this->imageResized);

		// Now crop from center to exact requested size
		$this->imageResized = imagecreatetruecolor($newWidth , $newHeight);
		imagecopyresampled($this->imageResized, $crop, 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight, $newWidth, $newHeight);
	}
}