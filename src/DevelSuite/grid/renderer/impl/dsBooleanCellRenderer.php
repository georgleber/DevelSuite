<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\grid\renderer\impl;

use DevelSuite\util\dsStringTools;

/**
 * FIXME
 *
 * @package DevelSuite\grid\renderer\impl
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsBooleanCellRenderer extends dsACellRenderer {
	public function setValue($value) {
		$this->value = dsStringTools::isBoolean($value);
	}

	public function render() {
		$image = NULL;
		if ($this->value) {
			$image = "<img src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAuklEQVR42qWScQfFIBTF27dNzIxMxtREkpjE7Nu+98q7U+96T0/nj3Vv55wfsYF0akgfpdTj3+KrM1QAKWVzed93DNi2rRmgtcaAdV1/lowxBDJpRgAhxNeytTafkEk7AizLkk3nHIEZdlCZQQDOeTa99/lMO8ywg9I9AszznM3jONATwAOlDAJM03QHQgj3XN6XPgKM41iFYozk8670EIAx1vgXEHKeJwZQSpsB13VhQHP7rQrQo27AE+MRcBFOD9LhAAAAAElFTkSuQmCC' width='16' />";
		} else {
			$image = "<img src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAiUlEQVR42qXM3QYFIRiF4d3dJpIhI5FKJCOSSHe7f9gn4ztZo3Wwzt6HvTbHfhdCeD8Nvw27Ad57OI4xUsA5BwMpJQpYa2Eg50wBYwwMlFIocJ4nDFzXRQGtNQzUWilwHAcMtNYooJSCgd47BaSUMDDGoIAQAgbmnBTgnMPAWosCcP3fDdjZNvABvRhVEQglsV8AAAAASUVORK5CYII=' width='16' />";
		}
		
		return $image;
	}
}