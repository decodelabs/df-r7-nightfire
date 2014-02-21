<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\nightfire\elements\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class IndexHeaderBar extends arch\component\template\HeaderBar {

    protected $_icon = 'element';

    protected function _getDefaultTitle() {
        return $this->_('Elements');
    }

    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri->request('~admin/nightfire/elements/add', true),
                    $this->_('Add element')
                )
                ->setIcon('add')
                ->addAccessLock('axis://nightfire/Element#add')
        );
    }
}