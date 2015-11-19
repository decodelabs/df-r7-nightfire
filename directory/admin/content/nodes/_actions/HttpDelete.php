<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\nodes\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpDelete extends arch\action\DeleteForm {

    const ITEM_NAME = 'node';

    protected $_node;

    protected function init() {
        $this->_node = $this->scaffold->getRecord();
    }

    protected function getInstanceId() {
        return $this->_node['id'];
    }

    protected function loadDelegates() {
        $this->_node->getType()->loadDeleteFormDelegate($this, 'type', $this->_node);
    }

    protected function createItemUi($container) {
        $container->addAttributeList($this->_node)
            ->addField('title')
            ->addField('slug')
            ->addField('type')
            ->addField('owner', function($node) {
                return $this->apex->component('~admin/users/clients/UserLink', $node['owner']);
            })
            ->addField('isLive', function($node) {
                return $this->html->booleanIcon($node['isLive']);
            })
            ->addField('creationDate', $this->_('Created'), function($node) {
                return $this->html->timeSince($node['creationDate']);
            })
            ->addField('currentVersion', $this->_('Version'), function($node) {
                if(!$node['versionCount']) {
                    return;
                }

                return $this->_('%v% of %c%', [
                    '%v%' => $node['currentVersion'],
                    '%c%' => $node['versionCount']
                ]);
            })
            ->addField('preview', function($node) {
                return $node->getType()->renderPreview($this->view, $node);
            })
            ;

        if($this->hasDelegate('type')) {
            $container->push($this['type']);
        }
    }

    protected function apply() {
        if($this->hasDelegate('type')) {
            $delegate = $this['type'];
            $delegate->validate();
            $delegate->apply();
        }

        $this->_node->delete();
    }
}