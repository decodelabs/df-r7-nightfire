<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\nightfire\page;

use df\axis;

class Unit extends axis\unit\Table
{
    protected function createSchema($schema)
    {
        $schema->addPrimaryField('id', 'Guid');
        $schema->addField('node', 'One', 'node');
        $schema->addField('owner', 'One', 'user/client');

        $schema->addField('title', 'Text', 255);

        $schema->addField('description', 'Text', 255)
            ->isNullable(true);

        $schema->addField('date', 'Timestamp');

        $schema->addField('body', 'Text', 'huge');
    }
}
