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
    
class ElementLink extends arch\component\template\RecordLink {

    protected $_icon = 'element';

    protected function _getRecordId() {
        return $this->_record['slug'];
    }

    protected function _getRecordName() {
        return $this->_record['slug'];
    }

    protected function _getRecordUrl($id) {
        return '~admin/content/elements/details?element='.$id;
    }
}