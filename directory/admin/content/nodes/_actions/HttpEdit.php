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

    protected $_versionId;

    protected function init() {
        $this->_node = $this->scaffold->getRecord();
        $this->_versionId = $this->request->query['version'];
    }

    protected function getInstanceId() {
        return $this->_node['id'].':'.$this->_versionId;
    }

    protected function initWithSession() {
        if(!$this->hasStore('nodeType')) {
            $this->setStore('nodeType', $this->_node['type']);
        }

        parent::initWithSession();
    }

    protected function loadDelegates() {
        if($this->_type) {
            $this->_type->loadEditFormDelegate($this, 'type', $this->_node, $this->_versionId, $this->_versionId ? true : false);
        }
    }

    protected function setDefaultValues() {
        $this->values->importFrom($this->_node, [
            'title', 'slug', 'defaultAccess', 'notes', 'isLive', 'isMappable'
        ]);
    }

    protected function afterInit() {
        if($this->isNew()) {
            $values = $this['type']->getDefaultNodeValues();

            if(is_array($values) && !empty($values)) {
                $this->values->import($values);
            }
        }
    }
}