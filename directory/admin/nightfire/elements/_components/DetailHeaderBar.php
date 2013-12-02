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
    
class DetailHeaderBar extends arch\component\template\HeaderBar {

    protected function _getDefaultTitle() {
        return $this->_('Element: %n%', [
            '%n%' => $this->_record['name']
        ]);
    }

    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            // Edit
            $this->import->component('ElementLink', '~admin/nightfire/elements/', $this->_record, $this->_('Edit element'))
                ->setAction('edit'),

            // Delete
            $this->import->component('ElementLink', '~admin/nightfire/elements/', $this->_record, $this->_('Delete element'))
                ->setAction('delete')
                ->setRedirectTo('~admin/nightfire/elements/')
        );
    }
}