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
    
class HttpDelete extends arch\form\template\Delete {

    const ITEM_NAME = 'node';

    protected $_node;

    protected function _init() {
        $this->_node = $this->scaffold->getRecord();
    }

    protected function _getDataId() {
        return $this->_node['id'];
    }

    protected function _setupDelegates() {
        $this->_node->getType()->loadDeleteFormDelegate($this, 'type', $this->_node);
    }

    protected function _renderItemDetails($container) {
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
            $container->push($this->getDelegate('type'));
        }
    }

    protected function _deleteItem() {
        if($this->hasDelegate('type')) {
            $delegate = $this->getDelegate('type');
            $delegate->validate();
            $delegate->apply();
        }

        $this->_node->delete();
    }
}