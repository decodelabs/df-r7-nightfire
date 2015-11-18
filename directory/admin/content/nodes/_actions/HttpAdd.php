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
use df\fire;
use df\opal;

class HttpAdd extends arch\form\Action {

    protected $_node;
    protected $_type;

    protected function init() {
        $this->_node = $this->scaffold->newRecord();
    }

    protected function initWithSession() {
        $type = $this->getStore('nodeType');

        if($type) {
            $this->_type = fire\type\Base::factory($type);
        }
    }

    protected function loadDelegates() {
        if($this->_type) {
            $this->_type->loadAddFormDelegate($this, 'type', $this->_node);
        }
    }

    protected function setDefaultValues() {
        $this->values->type = 'Page';
        $this->values->defaultAccess = 'all';
        $this->values->isLive = true;
        $this->values->isMappable = true;
    }

    protected function afterReset() {
        $this->_type = null;
    }


// Ui
    protected function createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Node details'));

        // Type
        if($this->_type) {
            $fs->addField($this->_('Type'))->setErrorContainer($this->values->type)->push(
                $this->html->textbox('type', $this->_type->getName())
                    ->isDisabled(true),

                $this->html->eventButton('resetType', $this->_('Change type'))
                    ->setIcon('edit')
                    ->shouldValidate(false)
            );
        } else {
            $fs->addField($this->_('Type'))->push(
                $this->html->selectList('type', $this->values->type, $this->data->nightfire->node->getTypeOptionList())
                    ->isRequired(true)
                    ->setNoSelectionLabel($this->_('Select node type...')),

                $this->html->eventButton('selectType', $this->_('Select'))
                    ->setIcon('accept')
                    ->shouldValidate(false)
            );
        }

        // Title
        $fs->addField($this->_('Title'))->push(
            $this->html->textbox('title', $this->values->title)
                ->setMaxLength(255)
                ->isRequired(true)
        );

        // Slug
        $fs->addField($this->_('Slug'))->push(
            $this->html->textbox('slug', $this->values->slug)
                ->setMaxLength(255)
                ->setPlaceholder($this->_('Auto-generate from title'))
        );

        // Default access
        $fs->addField($this->_('Default access'))->push(
            $this->html->selectList('defaultAccess', $this->values->defaultAccess, $this->data->nightfire->node->getDefaultAccessOptionList())
                ->isRequired(true)
        );

        // Is live
        $fs->addField()->push(
            $this->html->checkbox('isLive', $this->values->isLive, $this->_(
                'This node is live and viewable in the front end'
            ))
        );

        // Mappable
        $fs->addField()->push(
            $this->html->checkbox('isMappable', $this->values->isMappable, $this->_(
                'This node should appear in site maps and auto-generated navigation'
            ))
        );


        // Notes
        $fs->addField($this->_('Notes'))->push(
            $this->html->textarea('notes', $this->values->notes)
                ->setMaxLength(400)
        );


        // Type
        if($this->_type) {
            $form->push($this['type']);
        }

        // Buttons
        $form->addDefaultButtonGroup();
    }


// Events
    protected function onSelectTypeEvent() {
        $validator = $this->data->newValidator()
            // Type
            ->addRequiredField('type', 'enum')
                ->setOptions(array_keys($this->data->nightfire->node->getTypeOptionList()))

            ->validate($this->values);

        if($this->isValid()) {
            $this->setStore('nodeType', $validator['type']);
        }
    }

    protected function onResetTypeEvent() {
        $this->setStore('nodeType', false);
    }


    protected function onSaveEvent() {
        $validator = $this->data->newValidator()

            // Title
            ->addRequiredField('title', 'text')

            // Slug
            ->addRequiredField('slug')
                ->setDefaultValueField('title')
                ->allowPathFormat(true)
                ->allowRoot(true)
                ->allowAreaMarker(true)
                ->setRecord($this->_node)
                ->shouldRenameOnConflict(false)
                ->setSanitizer(function($value) {
                    if(!strlen($value)) {
                        return $value;
                    }

                    $value = new arch\Request($value);
                    return $value->toSlug();
                })

            // Default access
            ->addRequiredField('defaultAccess', 'enum')
                ->setOptions($this->data->nightfire->node->getDefaultAccessOptions())

            // Is live
            ->addField('isLive', 'boolean')

            // Is mappable
            ->addField('isMappable', 'boolean')

            // Notes
            ->addField('notes', 'text')
                ->setMaxLength(400)

            ->validate($this->values)
            ->applyTo($this->_node);


        if(!$this->_type) {
            $this->values->type->addError('required', $this->_(
                'Please select a node type'
            ));

            return;
        }

        $this->_node->type = $this->_type->getName();
        $delegate = $this['type'];
        $typeHistory = $delegate->validate();

        if(!is_array($typeHistory)) {
            $typeHistory = [];
        }

        return $this->complete(function() use($delegate, $typeHistory) {
            $this->_node->setTypeHistory($typeHistory);

            if($this->_node->isNew()) {
                $this->_node->owner = $this->user->client->getId();
            } else {
                $this->_node->lastEditDate = 'now';
            }

            $this->_node->save();
            $this->_node->typeId = $delegate->apply();

            if($this->_type instanceof fire\type\IVersionedType) {
                $this->_node->versionCount = $this->_type->countVersions($this->_node);
                $this->_node->currentVersion = $this->_type->getVersionNumber($this->_node);
            }

            $this->_node->save();
            $this->comms->flashSaveSuccess('content node');
        });
    }
}