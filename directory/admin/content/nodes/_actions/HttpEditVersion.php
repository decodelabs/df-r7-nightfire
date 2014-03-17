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

    protected function _init() {
        $this->_node = $this->data->fetchForAction(
            'axis://nightfire/Node',
            $this->request->query['node'],
            'edit'
        );

        $this->_type = $this->_node->getType();

        if(!$this->_type instanceof fire\type\IVersionedType) {
            $this->throwError(403, 'Type is not versioned');
        }
        
        $this->_versionId = $this->request->query['version'];

        if(!$this->_versionId) {
            $this->throwError(404, 'Version not found');
        }
    }

    protected function _getDataId() {
        return $this->_node['id'].':'.$this->_versionId;
    }

    protected function _setupDelegates() {
        $this->_type->loadEditFormDelegate($this, 'type', $this->_node, $this->_versionId, false);
    }


// Ui
    protected function _createUi() {
        $form = $this->content->addForm();

        // Type
        $this->getDelegate('type')->renderContainerContent($form);

        // Buttons
        $form->push($this->html->defaultButtonGroup());
    }


// Events
    protected function _onSaveEvent() {
        $delegate = $this->getDelegate('type');
        $delegate->validate();

        if($this->isValid()) {
            $delegate->apply();
            $this->_node->save();

            $this->comms->flash(
                'node.save',
                $this->_('The content node version has been successfully saved'),
                'success'
            );

            return $this->complete();
        }
    }
}