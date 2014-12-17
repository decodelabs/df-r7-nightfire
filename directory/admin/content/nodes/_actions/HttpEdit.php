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
    
class HttpEdit extends HttpAdd {

    protected function _init() {
        $this->_node = $this->data->fetchForAction(
            'axis://nightfire/Node',
            $this->request->query['node'],
            'edit'
        );

        $this->_versionId = $this->request->query['version'];
    }

    protected function _getDataId() {
        return $this->_node['id'].':'.$this->_versionId;
    }

    protected function _onSessionReady() {
        if(!$this->hasStore('nodeType')) {
            $this->setStore('nodeType', $this->_node['type']);  
        }

        parent::_onSessionReady();
    }

    protected function _setupDelegates() {
        if($this->_type) {
            $this->_type->loadEditFormDelegate($this, 'type', $this->_node, $this->_versionId, $this->_versionId ? true : false);
        }
    }

    protected function _setDefaultValues() {
        $this->values->importFrom($this->_node, [
            'title', 'slug', 'defaultAccess', 'notes', 'isLive', 'isMappable'
        ]);
    }

    protected function _onInitComplete() {
        if($this->isNew()) {
            $values = $this->getDelegate('type')->getDefaultNodeValues();

            if(is_array($values) && !empty($values)) {
                $this->values->import($values);
            }
        }
    }
}