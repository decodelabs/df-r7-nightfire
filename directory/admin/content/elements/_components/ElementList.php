<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\elements\_components;

use df;
use df\core;
use df\apex;
use df\arch;
use df\neon;

class ElementList extends arch\component\template\CollectionList {

    protected $_fields = [
        'slug' => true,
        'name' => true,
        'owner' => true,
        'creationDate' => true,
        'lastEditDate' => true,
        'actions' => true
    ];
    
// Slug
    public function addSlugField($list) {
        $list->addField('slug', function($element) {
            return $this->import->component('ElementLink', '~admin/content/elements/', $element)
                ->setRedirectFrom($this->_urlRedirect);
        });
    }

// Owner
    public function addOwnerField($list) {
        $list->addField('owner', function($element) {
            return $this->import->component('UserLink', '~admin/users/clients/', $element['owner'])
                ->setDisposition('transitive');
        });
    }

// Creation date
    public function addCreationDateField($list) {
        $list->addField('creationDate', $this->_('Created'), function($element) {
            return $this->html->timeSince($element['creationDate']);
        });
    }

// Last edit
    public function addLastEditDateField($list) {
        $list->addField('lastEditDate', $this->_('Edited'), function($element) {
            return $this->html->timeSince($element['lastEditDate']);
        });
    }

// Actions
    public function addActionsField($list) {
        $list->addField('actions', function($element) {
            return [
                // Edit
                $this->import->component('ElementLink', '~admin/content/elements/', $element, $this->_('Edit'))
                    ->setAction('edit'),

                // Delete
                $this->import->component('ElementLink', '~admin/content/elements/', $element, $this->_('Delete'))
                    ->setAction('delete')
            ];
        });
    }
}