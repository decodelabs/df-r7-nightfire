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
    
class PageAdd extends arch\form\template\NightfireTypeDelegate {

    protected $_layout;
    protected $_config;
    protected $_page;
    protected $_content;

    protected function _init() {
        $layout = $this->getStore('layout');
        $this->_config = fire\Config::getInstance();

        if(!$layout) {
            $list = $this->_config->getLayoutList();

            if(count($list) == 1) {
                reset($list);
                $layout = key($list);
                $this->setStore('layout', $layout);
            }
        }

        if($layout) {
            $this->_layout = $this->_config->getLayoutDefinition($layout);
        }

        $this->_page = $this->data->newRecord('axis://nightfire/Page');
    }

    protected function _setupDelegates() {
        if($this->_layout) {
            foreach($this->_layout->getSlots() as $slot) {
                $delegate = $this->loadDelegate('slot-'.$slot->getId(), '~/nightfire/ContentSlot')
                    ->isRequired($slot->getMinBlocks() > 0 || $slot->getId() == 'primary')
                    ->setSlotDefinition($slot);
            }
        }
    }

    public function renderContainerContent(aura\html\widget\IContainerWidget $form) {
        if(!$this->_layout) {
            return $this->_renderLayoutSelector($form);
        }

        $fs = $form->addFieldSet($this->_('Page details'));

        // Layout
        $fa = $fs->addFieldArea($this->_('Layout'))->setErrorContainer($this->values->layout)->push(
            $this->html->textbox('layout', $this->_layout->getName())
                ->isDisabled(true)
        );

        $list = $this->_config->getLayoutList();

        if(count($list) > 1) {
            $fa->push(
                $this->html->eventButton($this->eventName('resetLayout'), $this->_('Change layout'))
                    ->setIcon('edit')
                    ->shouldValidate(false)
            );
        }

        // Title
        if($this->_isSpecificVersion && !$this->_makeNew) {
            $fs->addFieldArea($this->_('Title'))->push(
                $this->html->textbox($this->fieldName('title'), $this->values->title)
                    ->setMaxLength(255)
                    ->isRequired(true)
            );
        }

        // Description
        $fs->addFieldArea($this->_('Description'))->push(
            $this->html->textbox($this->fieldName('description'), $this->values->description)
                ->setMaxLength(255)
        );

        // Keywords
        $fs->addFieldArea($this->_('Keywords'))->setDescription($this->_(
            'Separate by commas'
        ))->push(
            $this->html->textbox($this->fieldName('keywords'), $this->values->keywords)
                ->setPlaceholder($this->_('eg. about us, clients, work, company info'))
        );

        if(!$this->_page->isNew() && !$this->_isSpecificVersion) {
            // New version
            $fs->addFieldArea()->push(
                $this->html->checkbox($this->fieldName('makeNew'), $this->values->makeNew, $this->_(
                    'Turn any changes into a new version'
                ))
            );
        }

        foreach($this->_layout->getSlots() as $slot) {
            $form->addFieldSet($slot->getName())->push(
                $this->getDelegate('slot-'.$slot->getId())
            );
        }
    }

    protected function _renderLayoutSelector(aura\html\widget\IContainerWidget $form) {
        $fs = $form->addFieldSet($this->_('Page details'));

        // Layout
        $fs->addFieldArea($this->_('Layout'))->push(
            $this->html->selectList($this->fieldName('layout'), $this->values->layout, $this->_config->getLayoutList())
                ->isRequired(true)
                ->setNoSelectionLabel($this->_('Select layout...')),

            $this->html->eventButton($this->eventName('selectLayout'), $this->_('Select'))
                ->setIcon('accept')
                ->shouldValidate(false)
        );
    }


// Events
    protected function _onSelectLayoutEvent() {
        $validator = $this->data->newValidator()

            // Layout
            ->addRequiredField('layout', 'Enum')
                ->setOptions(array_keys($this->_config->getLayoutList()))

            ->validate($this->values);

        if($this->isValid()) {
            $this->setStore('layout', $validator['layout']);
        }
    }

    protected function _onResetLayoutEvent() {
        $this->setStore('layout', false);
    }



// IO
    public function validate() {
        if(!$this->_layout) {
            $this->values->layout->addError('required', $this->_(
                'Please select a layout'
            ));   

            return;
        }

        $validator = $this->data->newValidator()

            // Title
            ->chainIf($this->_isSpecificVersion && !$this->_makeNew, function($validator) {
                $validator->addRequiredField('title', 'text')
                    ->setMaxLength(255);
            })

            // Description
            ->addField('description', 'text')
                ->setMaxLength(255)

            // Keywords
            ->addField('keywords', 'text')
                ->setSanitizer(function($value) {
                    if(!strlen($value)) {
                        return $value;
                    }

                    $value = core\string\Util::parseDelimited($value);
                    return implode(',', $value);
                })

            ->validate($this->values)
            ->applyTo($this->_page);

        $slots = [];

        foreach($this->_layout->getSlots() as $slot) {
            $slots[$slot->getId()] = $this->getDelegate('slot-'.$slot->getId())->apply();
        }

        if($this->isValid()) {
            $layoutContent = new fire\layout\Content($this->_layout->getId());
            $layoutContent->addSlots($slots);
            $this->_page->body = $layoutContent->toXmlString(true);

            if(!($this->_isSpecificVersion && !$this->_makeNew)) {
                $this->_page->title = $this->_node['title'];
            }

            if($this->_page->isNew()) {
                $this->_page->owner = $this->user->client->getId();
                $this->_page->node = $this->_node;
            } else {
                if(($this->_makeNew || $this->values['makeNew']) && $this->_page->hasChanged()) {
                    $this->_page->makeNew([
                        'date' => null
                    ]);
                } else if($this->_page['id'] == $this->_node['typeId']) {
                    $this->_node->title = $this->_page['title'];
                }
            }
        }
    }

    public function apply() {
        $this->_page->save();
        return $this->_page['id'];
    }
}