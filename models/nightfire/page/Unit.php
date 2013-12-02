<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\nightfire\page;

use df;
use df\core;
use df\apex;
use df\axis;
use df\opal;

class Unit extends axis\unit\table\Base {

    protected function _onCreate(axis\schema\ISchema $schema) {
        $schema->addPrimaryField('id', 'AutoId', 8);
        $schema->addField('node', 'One', 'node');
        $schema->addField('owner', 'One', 'user/client');

        $schema->addField('title', 'String', 255);

        $schema->addField('description', 'String', 255)
            ->isNullable(true);
        $schema->addField('keywords', 'String', 255)
            ->isNullable(true);

        $schema->addField('date', 'Timestamp');

        $schema->addField('body', 'BigString', 'huge');
    }
}