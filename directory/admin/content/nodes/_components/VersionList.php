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

    protected $_node;

    public function setNode($node) {
        $this->_node = $node;
        return $this;
    }

    public function getNode() {
        return $this->_node;
    }
    

// Numver
    public function addNumberField($list) {
        $list->addField('number', '#', function($version, $context) {
            return count($this->_collection) - $context->getCounter();
        });
    }

// Owner
    public function addOwnerField($list) {
        $list->addField('owner', function($version) {
            return $this->apex->component('~admin/users/clients/UserLink', $version->getOwner())
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
            if($this->_node) {
                if(!($isActive = $version->isActive($this->_node))) {
                    $context->getRowTag()->addClass('disabled');
                }
            } else {
                $isActive = true;
            }

            return $this->html->booleanIcon($isActive);
        });
    }

// Actions
    public function addActionsField($list) {
        if(!$this->_node) {
            return;
        }

        $list->addField('actions', function($version) {
            $isActive = $version->isActive($this->_node);

            return [
                // Preview
                $this->html->link(
                        $this->uri('~admin/content/nodes/preview?node='.$this->_node->getId().'&version='.$version->getId()),
                        $this->_('Preview')
                    )
                    ->setIcon('preview')
                    ->setDisposition('transitive')
                    ->setTarget('_blank'),

                // Activate
                $this->html->link(
                        $this->uri('~admin/content/nodes/activate-version?node='.$this->_node->getId().'&version='.$version->getId(), true), 
                        $this->_('Activate')
                    )
                    ->setIcon('accept')
                    ->setDisposition('positive')
                    ->isDisabled($isActive),

                // Copy
                $this->html->link(
                        $this->uri('~admin/content/nodes/edit?node='.$this->_node->getId().'&version='.$version->getId(), true), 
                        $this->_('Copy')
                    )
                    ->setIcon('clipboard')
                    ->setDisposition('positive'),

                // Edit
                $this->html->link(
                        $this->uri('~admin/content/nodes/edit-version?node='.$this->_node->getId().'&version='.$version->getId(), true), 
                        $this->_('Edit')
                    )
                    ->setIcon('edit'),

                // Delete
                $this->html->link(
                        $this->uri('~admin/content/nodes/delete-version?node='.$this->_node->getId().'&version='.$version->getId(), true), 
                        $this->_('Delete')
                    )
                    ->setIcon('delete')
                    ->isDisabled($isActive)
            ];
        });
    }
}