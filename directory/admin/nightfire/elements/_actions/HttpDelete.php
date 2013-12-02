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
    
class HttpDelete extends arch\form\template\Delete {

    const ITEM_NAME = 'element';

    protected $_element;

    protected function _init() {
        $this->_element = $this->data->fetchForAction(
            'axis://nightfire/Element',
            $this->request->query['element'],
            'delete'
        );
    }

    protected function _getDataId() {
        return $this->_element['id'];
    }

    protected function _renderItemDetails($container) {
        $container->addAttributeList($this->_element)
            ->addField('slug')
            ->addField('name')
            ->addField('owner', function($element) {
                return $this->import->component('UserLink', '~admin/users/clients/', $element['owner'])
                    ->setDisposition('transitive');
            })
            ->addField('creationDate', $this->_('Created'), function($element) {
                return $this->html->timeSince($element['creationDate']);
            })
            ->addField('lastEditDate', $this->_('Last edited'), function($element) {
                return $this->html->timeSince($element['lastEditDate']);
            })
            ->addField('body', function($element) {
                return $this->nightfire->renderSlotPreview($element['body']);
            });
    }

    protected function _deleteItem() {
        $this->_element->delete();
    }
}