<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\study\classes\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class DetailHeaderBar extends arch\component\template\HeaderBar {

    protected function _getDefaultTitle() {
        return $this->_('Class: %n%', [
            '%n%' => $this->_record['name']
        ]);
    }

    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            // Edit
            $this->import->component('ClassLink', '~admin/study/classes/', $this->_record, $this->_('Edit class'))
                ->setAction('edit'),

            // Delete
            $this->import->component('ClassLink', '~admin/study/classes/', $this->_record, $this->_('Delete class'))
                ->setAction('delete')
                ->setRedirectTo('~admin/study/classes/')
        );
    }

    protected function _addSubOperativeLinks($menu) {
        if($this->request->isAction('sessions')) {
            $menu->addLinks(
                // Add session
                $this->html->link(
                        $this->uri->request('~admin/study/classroom-sessions/add?class='.$this->_record['id'], true),
                        $this->_('Add session')
                    )
                    ->setIcon('add'),

                // Reorder papers
                $this->html->link(
                        $this->uri->request('~admin/study/classroom-sessions/reorder?class='.$this->_record['id'], true),
                        $this->_('Re-order sessions')
                    )
                    ->setIcon('reorder')
                    ->setDisposition('operative')
            );
        }
    }

    protected function _addSectionLinks($menu) {
        $sessionCount = $this->_record->sessions->select()->count();

        $menu->addLinks(
            // Details
            $this->import->component('ClassLink', '~admin/study/classes/', $this->_record, $this->_('Details'), true)
                ->setAction('details')
                ->setIcon('details'),

            // Contacts
            $this->import->component('ClassLink', '~admin/study/classes/', $this->_record, $this->_('Sessions'), true)
                ->setAction('sessions')
                ->setIcon('session')
                ->setNote($this->format->counterNote($sessionCount))
        );
    }
}