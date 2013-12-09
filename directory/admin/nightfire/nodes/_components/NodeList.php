<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\nightfire\nodes\_components;

use df;
use df\core;
use df\apex;
use df\arch;
use df\neon;

class NodeList extends arch\component\template\CollectionList {

    protected $_fields = [
        'slug' => true,
        'title' => true, 
        'type' => true,
        'owner' => true,
        'creationDate' => true,
        'lastEditDate' => true,
        'currentVersion' => true,
        'isLive' => true,
        'actions' => true
    ];
    

// Slug
    public function addSlugField($list) {
        $list->addField('slug', function($node) {
            return $this->import->component('NodeLink', '~admin/nightfire/nodes/', $node)
                ->setRedirectFrom($this->_urlRedirect);
        });
    }

// Owner
    public function addOwnerField($list) {
        $list->addField('owner', function($node) {
            return $this->import->component('UserLink', '~admin/users/clients/', $node['owner'])
                ->setDisposition('transitive');
        });
    }

// Creation date
    public function addCreationDateField($list) {
        $list->addField('creationDate', $this->_('Created'), function($node) {
            return $this->html->timeSince($node['creationDate']);
        });
    }

// Last edit date
    public function addLastEditDateField($list) {
        $list->addField('lastEditDate', $this->_('Edited'), function($node) {
            return $this->html->timeSince($node['lastEditDate']);
        });
    }

// Versions
    public function addCurrentVersionField($list) {
        $list->addField('currentVersion', 'C', function($node) {
            if(!$node['versionCount']) {
                return;
            }

            return $node['currentVersion'].' / '.$node['versionCount'];
        });

        $list->addLabel('currentVersion', 'versionCount', 'V');
    }

// Is live
    public function addIsLiveField($list) {
        $list->addField('isLive', $this->_('Live'), function($node, $context) {
            if(!$node['isLive']) {
                $context->getRowTag()->addClass('state-disabled');
            }

            return $this->html->booleanIcon($node['isLive']);
        });
    }

// Actions
    public function addActionsField($list) {
        $list->addField('actions', function($node) {
            return [
                // Preview
                $this->import->component('NodeLink', '~admin/nightfire/nodes/', $node, $this->_('Preview'))
                    ->setAction('preview')
                    ->setDisposition('transitive')
                    ->setIcon('preview')
                    ->render()
                        ->setAttribute('target', '_blank'),

                // Edit
                $this->import->component('NodeLink', '~admin/nightfire/nodes/', $node, $this->_('Edit'))
                    ->setAction('edit'),

                // Delete
                $this->import->component('NodeLink', '~admin/nightfire/nodes/', $node, $this->_('Delete'))
                    ->setAction('delete')
            ];
        });
    }
}