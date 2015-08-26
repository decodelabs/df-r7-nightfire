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

class Unit extends axis\unit\table\Base {

    protected $_defaultSearchFields = [
        'slug' => 3,
        'title' => 1
    ];

    protected $_defaultOrderableFields = [
        'slug', 'title', 'type', 'creationDate', 'lastEditDate',
        'owner', 'currentVersion', 'versionCount', 'defaultAccess', 'isMappable', 'isLive'
    ];

    protected $_defaultOrder = 'slug ASC';

    protected function createSchema($schema) {
        $schema->addPrimaryField('id', 'Guid');
        $schema->addField('slug', 'Slug')
            ->allowPathFormat(true);

        $schema->addField('title', 'String', 255);

        $schema->addField('type', 'String', 64);
        $schema->addField('typeId', 'Guid')
            ->isNullable(true);
        $schema->addField('typeData', 'String', 1024)
            ->isNullable(true);

        $schema->addField('creationDate', 'Timestamp');
        $schema->addField('lastEditDate', 'DateTime')
            ->isNullable(true);

        $schema->addField('owner', 'One', 'user/client');

        $schema->addField('versionCount', 'Integer', 2)
            ->isNullable(true);
        $schema->addField('currentVersion', 'Integer', 2)
            ->isNullable(true);

        $schema->addField('defaultAccess', 'Enum')
            ->setOptions(['all', 'none', 'deactivated', 'guest', 'pending', 'bound', 'confirmed', 'dev'])
            ->setDefaultValue('all');

        $schema->addField('notes', 'String', 400)
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

    public function getDefaultAccessOptionList() {
        return [
            'all' => $this->context->_('Everyone'),
            'none' => $this->context->_('No one (requires access keys)'),
            
            'deactivated' => $this->context->_('Only deactivated users'),
            'guest' => $this->context->_('Only guests'),
            'pending' => $this->context->_('Only pending users'),
            'bound' => $this->context->_('Only logged in users'),
            'confirmed' => $this->context->_('Only logged in and password-confirmed users'),
            
            'dev' => $this->context->_('Development mode users')
        ];
    }

    public function getDefaultAccessOptions() {
        return $this->getUnitSchema()->getField('defaultAccess')->getOptions();
    }
}