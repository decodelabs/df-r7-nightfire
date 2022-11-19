<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\nightfire\accessOptions;

use df\axis;

class Unit extends axis\unit\Enum
{
    public const ALL = 'Everyone';
    public const NONE = 'No one (requires access key)';
    public const DEACTIVATED = 'Only deactivated users';
    public const GUEST = 'Only guests';
    public const PENDING = 'Only pending users';
    public const BOUND = 'Only logged in users';
    public const CONFIRMED = 'Only logged in and password-confirmed users';
    public const DEV = 'Development mode users';
}
