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

use DecodeLabs\Tagged as Html;

class VersionList extends arch\component\CollectionList
{
    protected $fields = [
        'number' => true,
        'title' => true,
        'owner' => true,
        'date' => true,
        'isActive' => true,
        'actions' => true
    ];

    protected $node;

    public function setNode($node)
    {
        $this->node = $node;
        return $this;
    }

    public function getNode()
    {
        return $this->node;
    }


    // Numver
    public function addNumberField($list)
    {
        $list->addField('number', '#', function ($version, $context) {
            return count($this->collection) - $context->getCounter();
        });
    }

    // Owner
    public function addOwnerField($list)
    {
        $list->addField('owner', function ($version) {
            return $this->apex->component('~admin/users/clients/UserLink', $version->getOwner());
        });
    }

    // Date
    public function addDateField($list)
    {
        $list->addField('date', $this->_('Created'), function ($version) {
            return Html::$time->since($version->getDate());
        });
    }

    // Is active
    public function addIsActiveField($list)
    {
        $list->addField('isActive', $this->_('Active'), function ($version, $context) {
            if ($this->node) {
                if (!($isActive = $version->isActive($this->node))) {
                    $context->getRowTag()->addClass('disabled');
                }
            } else {
                $isActive = true;
            }

            return $this->html->booleanIcon($isActive);
        });
    }

    // Actions
    public function addActionsField($list)
    {
        if (!$this->node) {
            return;
        }

        $list->addField('actions', function ($version) {
            $isActive = $version->isActive($this->node);

            return [
                // Preview
                $this->html->link(
                        $this->uri('./preview?node='.$this->node->getId().'&version='.$version->getId()),
                        $this->_('Preview')
                    )
                    ->setIcon('preview')
                    ->setDisposition('transitive')
                    ->setTarget('_blank'),

                // Activate
                $this->html->link(
                        $this->uri('./activate-version?node='.$this->node->getId().'&version='.$version->getId(), true),
                        $this->_('Activate')
                    )
                    ->setIcon('accept')
                    ->setDisposition('positive')
                    ->isDisabled($isActive),

                // Copy
                $this->html->link(
                        $this->uri('./edit?node='.$this->node->getId().'&version='.$version->getId(), true),
                        $this->_('Copy')
                    )
                    ->setIcon('clipboard')
                    ->setDisposition('positive'),

                // Edit
                $this->html->link(
                        $this->uri('./edit-version?node='.$this->node->getId().'&version='.$version->getId(), true),
                        $this->_('Edit')
                    )
                    ->setIcon('edit'),

                // Delete
                $this->html->link(
                        $this->uri('./delete-version?node='.$this->node->getId().'&version='.$version->getId(), true),
                        $this->_('Delete')
                    )
                    ->setIcon('delete')
                    ->isDisabled($isActive)
            ];
        });
    }
}
