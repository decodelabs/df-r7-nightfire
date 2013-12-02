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
use df\opal;
use df\fire;

class Unit extends axis\unit\table\Base {

    protected function _onCreate(axis\schema\ISchema $schema) {
        $schema->addField('id', 'AutoId', 8);
        $schema->addField('slug', 'Slug')
            ->allowPathFormat(true);

        $schema->addField('title', 'String', 255);

        $schema->addField('type', 'String', 64);
        $schema->addField('typeId', 'Integer', 8)
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

    public function applyPagination(opal\query\IPaginator $paginator) {
        $paginator
            ->setOrderableFields(
                'slug', 'title', 'type', 'creationDate', 'lastEditDate',
                'owner', 'currentVersion', 'versionCount', 'defaultAccess', 'isMappable', 'isLive'
            )
            ->setDefaultOrder('slug ASC');

        return $this;
    }

    public function load(arch\IContext $context) {
        $slug = $context->request->toSlug();

        if(!$node = $this->fetchBySlug($slug)) {
            $context->throwError(404, 'Node "'.$slug.'" not found');
        }

        if(!$node['isLive']) {
            $context->throwError(404, 'Node "'.$slug.'" is not live');
        }

        $type = $node->getType();
        return $type->createResponse($context, $node);
    }

    public function fetchBySlug($slug) {
        return $this->fetch()
            ->where('slug', '=', $slug)
            ->toRow();
    }

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