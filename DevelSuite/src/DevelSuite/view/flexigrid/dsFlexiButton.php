<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2011 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\view\flexigrid;

/**
 * FIXME
 *
 * @package DevelSuite\view\flexigrid
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsFlexiButton {
	private $id;
	private $name;
	private $bclass;
	private $onpress;
	private $needID = FALSE;
	private $needMultipleIDs = FALSE;
	private $callBack;

	public function __construct($id, $name, $bclass = NULL, $onpress = NULL, $callBack = NULL) {
		$this->id = $id;
		$this->name = $name;

		if (!isset($bclass)) {
			$this->bclass = $id;
		} else {
			$this->bclass = $bclass;
		}

		if (!isset($onpress)) {
			$this->onpress = "do" . ucfirst($id);
		} else {
			$this->onpress = $onpress;
		}

		if (!isset($callBack)) {
			$this->callBack = $id;
		} else {
			$this->callBack = $callBack;
		}
	}

	public function doNeedID() {
		$this->needMultipleIDs = FALSE;
		$this->needID = TRUE;
	}

	public function doNeedMultipleIDs() {
		$this->needID = FALSE;
		$this->needMultipleIDs = TRUE;
	}

	public function getID() {
		return $this->id;
	}

	public function getName() {
		return $this->name;
	}

	public function getBClass() {
		return $this->bclass;
	}

	public function getOnpress() {
		return $this->onpress;
	}

	public function needID() {
		return $this->needID;
	}

	public function needMultipleIDs() {
		return $this->needMultipleIDs;
	}

	public function getCallBack() {
		return $this->callBack;
	}
}