<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\nodes\_components;

use df;
use df\core;
use df\apex;
use df\arch;
use df\neon;

class VersionList extends arch\component\template\CollectionList {

    protected $_fields = [
        'number' => true,
        'title' => true, 
        'owner' => true,
        'date' => true,
        'isActive' => true,
        'actions' => true
    ];
    

// Numver
    public function addNumberField($list) {
        $list->addField('number', '#', function($version, $context) {
            return count($this->_collection) - $context->getCounter();
        });
    }

// Owner
    public function addOwnerField($list) {
        $list->addField('owner', function($version) {
            return $this->import->component('UserLink', '~admin/users/clients/', $version->getOwner())
                ->setDisposition('transitive');
        });
    }

// Date
    public function addDateField($list) {
        $list->addField('date', $this->_('Created'), function($version) {
            return $this->html->timeSince($version->getDate());
        });
    }

// Is active
    public function addIsActiveField($list) {
        $list->addField('isActive', $this->_('Active'), function($version, $context) {
            if(!($isActive = $version->isActive($this->getView()['node']))) {
                $context->getRowTag()->addClass('state-disabled');
            }

            return $this->html->booleanIcon($isActive);
        });
    }

// Actions
    public function addActionsField($list) {
        $list->addField('actions', function($version) {
            $node = $this->getView()['node'];
            $isActive = $version->isActive($node);

            return [
                // Preview
                $this->html->link(
                        $this->uri->request('~admin/content/nodes/preview?node='.$node->getId().'&version='.$version->getId()),
                        $this->_('Preview')
                    )
                    ->setIcon('preview')
                    ->setDisposition('transitive')
                    ->setTarget('_blank'),

                // Activate
                $this->html->link(
                        $this->uri->request('~admin/content/nodes/activate-version?node='.$node->getId().'&version='.$version->getId(), true), 
                        $this->_('Activate')
                    )
                    ->setIcon('accept')
                    ->setDisposition('positive')
                    ->isDisabled($isActive),

                // Copy
                $this->html->link(
                        $this->uri->request('~admin/content/nodes/edit?node='.$node->getId().'&version='.$version->getId(), true), 
                        $this->_('Copy')
                    )
                    ->setIcon('clipboard')
                    ->setDisposition('positive'),

                // Edit
                $this->html->link(
                        $this->uri->request('~admin/content/nodes/edit-version?node='.$node->getId().'&version='.$version->getId(), true), 
                        $this->_('Edit')
                    )
                    ->setIcon('edit'),

                // Delete
                $this->html->link(
                        $this->uri->request('~admin/content/nodes/delete-version?node='.$node->getId().'&version='.$version->getId(), true), 
                        $this->_('Delete')
                    )
                    ->setIcon('delete')
                    ->isDisabled($isActive)
            ];
        });
    }
}