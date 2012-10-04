<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\packages\nightfire;

use df\core;

class Package extends core\Package {
    
    const PRIORITY = 3;

    public static $dependencies = [
        'webCore',
        'media',
        'nightfireCore'
    ];
}