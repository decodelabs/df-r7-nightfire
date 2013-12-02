<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\nightfire\nodes\_actions;

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
    }

    protected function _getDataId() {
        return $this->_node['id'];
    }

    protected function _onSessionCreate() {
        if(!$this->hasStore('nodeType')) {
            $this->setStore('nodeType', $this->_node['type']);  
        }

        parent::_onSessionCreate();
    }

    protected function _setDefaultValues() {
        $this->values->importFrom($this->_node, [
            'title', 'slug', 'defaultAccess', 'notes', 'isLive', 'isMappable'
        ]);
    }
}