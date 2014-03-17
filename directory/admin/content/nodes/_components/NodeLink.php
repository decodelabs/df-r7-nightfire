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
    
class NodeLink extends arch\component\template\RecordLink {

    protected $_icon = 'node';

// Url
    protected function _getRecordUrl($id) {
        return '~admin/content/nodes/details?node='.$id;
    }

    protected function _getRecordName() {
        return $this->_record['slug'];
    }
}