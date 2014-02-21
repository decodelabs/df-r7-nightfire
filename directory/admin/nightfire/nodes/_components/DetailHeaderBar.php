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
use df\fire;
    
class DetailHeaderBar extends arch\component\template\HeaderBar {

    protected $_icon = 'node';

    protected function _getDefaultTitle() {
        return $this->_('Node: %n%', [
            '%n%' => $this->_record['title']
        ]);
    }

    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            // Preview
            $this->import->component('NodeLink', '~admin/nightfire/nodes/', $this->_record, $this->_('Preview'))
                ->setAction('preview')
                ->setDisposition('transitive')
                ->setIcon('preview')
                ->render()
                    ->setAttribute('target', '_blank'),

            // Edit
            $this->import->component('NodeLink', '~admin/nightfire/nodes/', $this->_record, $this->_('Edit node'))
                ->setAction('edit'),

            // Delete
            $this->import->component('NodeLink', '~admin/nightfire/nodes/', $this->_record, $this->_('Delete node'))
                ->setAction('delete')
                ->setRedirectTo('~admin/nightfire/nodes/')
        );
    }

    protected function _addSubOperativeLinks($menu) {
        if($this->request->isAction('versions')) {
            /*
            $menu->addLinks(
                // Add session
                $this->html->link(
                        $this->uri->request('~admin/study/classroom-sessions/add?class='.$this->_record['id'], true),
                        $this->_('Add session')
                    )
                    ->setIcon('add')
            );
            */
        }
    }

    protected function _addSectionLinks($menu) {
        $menu->addLinks(
            // Details
            $this->import->component('NodeLink', '~admin/nightfire/nodes/', $this->_record, $this->_('Details'), true)
                ->setAction('details')
                ->setIcon('details')
        );

        if($this->_record->getType() instanceof fire\type\IVersionedType) {
            $menu->addLinks(
                // Versions
                $this->import->component('NodeLink', '~admin/nightfire/nodes/', $this->_record, $this->_('Versions'), true)
                    ->setAction('versions')
                    ->setIcon('list')
                    ->setNote($this->format->counterNote($this->_record['versionCount']))
            );
        }
    }
}