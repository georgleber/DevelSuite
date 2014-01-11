<?php
/*
 * This file is part of the DevelSuite
 * Copyright (C) 2012 Georg Henkel <info@develman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DevelSuite\logging;

use Monolog\Logger;

/**
 * FIXME
 *
 * @package DevelSuite\mail
 * @author  Georg Henkel <info@develman.de>
 * @version 1.0
 */
class dsPropelLogger extends Logger {

    public function __construct($name) {
        parent::__construct($name);
    }
    
    public function emergency($message) {
        parent::emerg($message);
    }

    public function warning($message) {
        parent::warn($message);
    }

}
