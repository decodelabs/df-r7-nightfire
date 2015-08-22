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
    
class HttpEditVersion extends arch\form\Action {

    protected $_node;
    protected $_type;
    protected $_versionId;

    protected function init() {
        $this->_node = $this->scaffold->getRecord();
        $this->_type = $this->_node->getType();

        if(!$this->_type instanceof fire\type\IVersionedType) {
            $this->throwError(403, 'Type is not versioned');
        }
        
        $this->_versionId = $this->request->query['version'];

        if(!$this->_versionId) {
            $this->throwError(404, 'Version not found');
        }
    }

    protected function getInstanceId() {
        return $this->_node['id'].':'.$this->_versionId;
    }

    protected function loadDelegates() {
        $this->_type->loadEditFormDelegate($this, 'type', $this->_node, $this->_versionId, false);
    }


// Ui
    protected function createUi() {
        $form = $this->content->addForm();

        // Type
        $form->push($this->getDelegate('type'));

        // Buttons
        $form->addDefaultButtonGroup();
    }


// Events
    protected function onSaveEvent() {
        $delegate = $this->getDelegate('type');
        $delegate->validate();

        return $this->complete(function() use($delegate) {
            $delegate->apply();
            $this->_node->save();
            $this->comms->flashSaveSuccess('content node version');
        });
    }
}