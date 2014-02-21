<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\nightfire\nodes\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class IndexHeaderBar extends arch\component\template\HeaderBar {

    protected $_icon = 'node';

    protected function _getDefaultTitle() {
        return $this->_('Nodes');
    }

    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri->request('~admin/nightfire/nodes/add', true),
                    $this->_('Add node')
                )
                ->setIcon('add')
                ->addAccessLock('axis://nightfire/Node#add')
        );
    }
}