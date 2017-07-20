<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\nightfire\accessOptions;

use df;
use df\core;
use df\apex;
use df\axis;

class Unit extends axis\unit\Enum {

    const ALL = 'Everyone';
    const NONE = 'No one (requires access key)';
    const DEACTIVATED = 'Only deactivated users';
    const GUEST = 'Only guests';
    const PENDING = 'Only pending users';
    const BOUND = 'Only logged in users';
    const CONFIRMED = 'Only logged in and password-confirmed users';
    const DEV = 'Development mode users';
}
