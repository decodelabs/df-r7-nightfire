<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\nightfire\elements\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpEdit extends HttpAdd {

    protected function _init() {
        $this->_element = $this->data->fetchForAction(
            'axis://nightfire/Element',
            $this->request->query['element'],
            'edit'
        );
    }

    protected function _getDataId() {
        return $this->_element['id'];
    }

    protected function _setDefaultValues() {
        $this->values->importFrom($this->_element, [
            'slug', 'name'
        ]);

        $this->getDelegate('body')->setSlotContent($this->_element['body']);
    }
}