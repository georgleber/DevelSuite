<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\pagination;

/**
 * FIXME
 *
 * @package DevelSuite\pagination
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsPager {
	private $pageSet;
	private $currentPage;
	private $max = 0;
	private $rowPagination;
	private $returnArrayVal;

	public function __construct(array $pageSet, $currentPage, $rowPagination = TRUE, $returnArrayVal = FALSE) {
		$this->pageSet = $pageSet;
		$this->currentPage = $currentPage;
		$this->rowPagination = $rowPagination;
		$this->returnArrayVal = $returnArrayVal;
	}

	public function getPage() {
		if($this->returnArrayVal) {
			$arr = array_keys($this->pagingArray, $this->currentPage);
			return $this->pagingArray[$arr[0]];
		} else {
			return $this->currentPage;
		}
	}

	public function getTotalPages() {
		$count = count($this->pagingArray);
		if($this->rowPagination) {
			if ($this->max > 0) {
				$pages = ceil($count/$this->max);
			} else {
				$pages = 0;
			}
		} else {
			$pages = $count;
		}

		return $pages;
	}

	public function getFirstPage() {
		if($this->returnArrayVal) {
			return $this->pagingArray[0];
		} else {
			return 1;
		}
	}

	public function atFirstPage() {
		return $this->getPage() == $this->getFirstPage();
	}

	public function getLastPage() {
		$totalPages = $this->getTotalPages();
		if($this->returnArrayVal) {
			return $this->pagingArray[$totalPages - 1];
		} else {
			return $totalPages;
		}
	}

	public function atLastPage() {
		return $this->getPage() == $this->getLastPage();
	}

	public function getPrev() {
		if ($this->getPage() != $this->getFirstPage()) {
			for($i = 0, $arrCount = count($this->pagingArray); $i < $arrCount; $i++) {
				if($this->pagingArray[$i] == $this->getPage()) {
					break;
				}
			}

			$prev = $i - 1;
		} else {
			$prev = 0;
		}

		return $this->pagingArray[$prev];
	}

	public function getPrevPages($range = 5) {
		$total = $this->getTotalPages();
		$start = $this->getPage() - 1;
		$end = $this->getPage() - $range;
		$first =  $this->getFirstPage();
		$pages = array();

		for ($i = $start; $i >= $end; $i--) {
			if ($i < $first) {
				break;
			}

			$pages[] = $i;
		}

		return array_reverse($pages);
	}

	public function getNext() {
		if ($this->getPage() != $this->getLastPage()) {
			for($i = 0, $arrCount = count($this->pagingArray); $i < $arrCount; $i++) {
				if($this->pagingArray[$i] == $this->getPage()) {
					break;
				}
			}

			$next = $i + 1;
		} else {
			$next = 0;
		}

		return $this->pagingArray[$next];
	}

	public function getNextPages($range = 5) {
		$total = $this->getTotalPages();
		$start = $this->getPage() + 1;
		$end = $this->getPage() + $range;
		$last =  $this->getLastPage();

		$pages = array();
		for ($i = $start; $i <= $end; $i++) {
			if ($i > $last) {
				break;
			}

			$pages[] = $i;
		}

		return $pages;
	}

	public function setCountPerPage($pageCount) {
		$this->max = $pageCount;
	}

	public function getCountPerPage() {
		return $this->max;
	}
}