<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\shared\nightfire\_formDelegates\types;

use df;
use df\core;
use df\apex;
use df\arch;
use df\fire;
    
class PageEdit extends PageAdd {

    protected $_content;

    protected function _init() {
        $this->_page = $this->data->fetchForAction(
            'axis://nightfire/Page',
            $this->_versionId,
            'edit'
        );

        $this->_config = fire\Config::getInstance();
        $this->_content = fire\layout\Content::fromXmlString($this->_page['body']);

        if(!$this->hasStore('layout')) {
            $this->setStore('layout', $this->_content->getId());
        }

        $layout = $this->getStore('layout');

        if($layout) {
            if($layout != $this->_content->getId()) {
                $this->_content = null;
            }

            $this->_layout = $this->_config->getLayoutDefinition($layout);
        }
    }

    protected function _setDefaultValues() {
        $this->values->importFrom($this->_page, [
            'title', 'description', 'keywords'
        ]);

        if($this->_layout && $this->_content) {
            foreach($this->_layout->getSlots() as $slot) {
                $this->getDelegate('slot-'.$slot->getId())->setSlotContent($this->_content->getSlot($slot->getId()));
            }
        }
    }

    public function getDefaultNodeValues() {
        return ['title' => $this->_page['title']];
    }
}