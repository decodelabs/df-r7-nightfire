<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\nightfire\node;

use df;
use df\core;
use df\apex;
use df\axis;
use df\arch;
use df\fire;

class Unit extends axis\unit\Table {

    const SEARCH_FIELDS = [
        'slug' => 3,
        'title' => 1
    ];

    const ORDERABLE_FIELDS = [
        'slug', 'title', 'type', 'creationDate', 'lastEditDate',
        'owner', 'currentVersion', 'versionCount', 'defaultAccess', 'isMappable', 'isLive'
    ];

    const DEFAULT_ORDER = 'slug ASC';

    protected function createSchema($schema) {
        $schema->addPrimaryField('id', 'Guid');
        $schema->addField('slug', 'Slug')
            ->allowPathFormat(true);

        $schema->addField('title', 'Text', 255);

        $schema->addField('type', 'Text', 64);
        $schema->addField('typeId', 'Guid')
            ->isNullable(true);
        $schema->addField('typeData', 'Text', 1024)
            ->isNullable(true);

        $schema->addField('creationDate', 'Timestamp');
        $schema->addField('lastEditDate', 'Date:Time')
            ->isNullable(true);

        $schema->addField('owner', 'One', 'user/client');

        $schema->addField('versionCount', 'Number', 2)
            ->isNullable(true);
        $schema->addField('currentVersion', 'Number', 2)
            ->isNullable(true);

        $schema->addField('defaultAccess', 'Enum')
            ->setOptions(['all', 'none', 'deactivated', 'guest', 'pending', 'bound', 'confirmed', 'dev'])
            ->setDefaultValue('all');
        $schema->addField('accessSignifiers', 'Json', 'medium')
            ->isNullable(true);

        $schema->addField('notes', 'Text', 400)
            ->isNullable(true);

        $schema->addField('isMappable', 'Boolean')
            ->setDefaultValue(true);
        $schema->addField('isLive', 'Boolean');
    }

    public function load(arch\IRequest $request) {
        return $this->fetch()
            ->where('slug', '=', $request->toSlug())
            ->where('isLive', '=', true)
            ->toRow();
    }

    public function exists(arch\IRequest $request) {
        return (bool)$this->select('id')
            ->where('slug', '=', $request->toSlug())
            ->where('isLive', '=', true)
            ->count();
    }

    /*
    public function fetchBySlug($slug) {
        return $this->fetch()
            ->where('slug', '=', $slug)
            ->toRow();
    }
    */

    public function getTypeOptionList() {
        $output = [];

        foreach(fire\type\Base::loadAll() as $type) {
            $output[$type->getName()] = $type->getDisplayName();
        }

        return $output;
    }
}