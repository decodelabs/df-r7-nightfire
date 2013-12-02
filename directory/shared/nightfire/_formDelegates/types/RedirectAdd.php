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
use df\aura;
    
class RedirectAdd extends arch\form\template\NightfireTypeDelegate {


    protected function _setDefaultValues() {
        $this->values->url = $this->_node['typeData'];
    }

    public function renderContainerContent(aura\html\widget\IContainerWidget $form) {
        $fs = $form->addFieldSet($this->_('Redirect details'));

        // Url
        $fs->addFieldArea($this->_('Url'))->push(
            $this->html->textbox($this->fieldName('url'), $this->values->url)
                ->isRequired(true)
        );  
    }

    public function validate() {
        $this->data->newValidator()
            ->addField('url', 'text')
                ->isRequired(true)
                ->setRecordName('typeData')
                ->end()
            ->validate($this->values)
            ->applyTo($this->_node);
    }

    public function apply() {}
}