<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\elements\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpAdd extends arch\form\Action {

    protected $_element;

    protected function _init() {
        $this->_element = $this->data->newRecord('axis://nightfire/Element', [
            'owner' => $this->user->client->getId()
        ]);
    }

    protected function _setupDelegates() {
        $this->loadDelegate('body', 'ContentSlot', '~/nightfire/')
            ->isRequired(true)
            ->setSlotDefinition($this->_element->getSlotDefinition());
    }

    protected function _createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Element details'));

        // Name
        $fs->addFieldArea($this->_('Name'))->push(
            $this->html->textbox('name', $this->values->name)
                ->isRequired(true)
        );

        // Slug
        $fs->addFieldArea($this->_('Slug'))->setDescription($this->_(
            'Leave empty to generate from name'
        ))->push(
            $this->html->textbox('slug', $this->values->slug)
        );

        // Body
        $form->push($this->getDelegate('body')->renderFieldSet($this->_('Body')));

        // Buttons
        $form->push($this->html->defaultButtonGroup());
    }

    protected function _onSaveEvent() {
        $this->_element->body = $this->getDelegate('body')->apply();

        $validator = $this->data->newValidator()

            // Name
            ->addField('name', 'text')
                ->isRequired(true)
                ->end()

            // Slug
            ->addField('slug', 'Slug')
                ->setDefaultValueField('name')
                ->setStorageAdapter($this->_element->getRecordAdapter())
                ->setUniqueFilterId($this->_element['slug'])
                ->isRequired(true)
                ->end()

            ->validate($this->values)
            ->applyTo($this->_element);

        if($this->isValid()) {
            if(!$this->_element->isNew()) {
                $this->_element->lastEditDate = 'now';
            }

            $this->_element->save();

            $this->comms->flash(
                'element.save',
                $this->_('The element has been successfully saved'),
                'success'
            );

            return $this->complete();
        }
    }
}