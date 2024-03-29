<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\shared\nightfire\_formDelegates\types;

use DecodeLabs\Disciple;
use DecodeLabs\R7\Config\Nightfire as NightfireConfig;
use DecodeLabs\Tagged as Html;
use df\apex\directory\shared\nightfire\_formDelegates\ContentSlot;
use df\arch;
use df\aura;
use df\fire;

class PageAdd extends arch\node\form\NightfireTypeDelegate
{
    protected $_layout;
    protected $_config;
    protected $_page;
    protected $_content;

    protected function init(): void
    {
        $layout = $this->getStore('layout');
        $this->_config = NightfireConfig::load();

        if (!$layout) {
            $list = $this->_config->getLayoutList();

            if (count($list) == 1) {
                reset($list);
                $layout = key($list);
                $this->setStore('layout', $layout);
            }
        }

        if ($layout) {
            $this->_layout = $this->_config->getLayoutDefinition($layout);
        }

        $this->_page = $this->data->newRecord('axis://nightfire/Page');
    }

    protected function loadDelegates(): void
    {
        if ($this->_layout) {
            foreach ($this->_layout->getSlots() as $slot) {
                $this->loadDelegate('slot-' . $slot->getId(), '~/nightfire/ContentSlot')
                    ->as(ContentSlot::class)
                    ->isRequired($slot->getMinBlocks() > 0 || $slot->getId() == 'primary')
                    ->setSlotDefinition($slot);
            }
        }
    }

    public function renderContainerContent(aura\html\widget\IContainerWidget $form)
    {
        if (!$this->_layout) {
            return $this->_renderLayoutSelector($form);
        }

        $fs = $form->addFieldSet($this->_('Page details'));

        // Layout
        $fa = $fs->addField($this->_('Layout'))->setErrorContainer($this->values->layout)->push(
            $this->html->textbox('layout', $this->_layout->getName())
                ->isDisabled(true)
        );

        $list = $this->_config->getLayoutList();

        if (count($list) > 1) {
            $fa->push(
                $this->html->eventButton($this->eventName('resetLayout'), $this->_('Change layout'))
                    ->setIcon('edit')
                    ->shouldValidate(false)
            );
        }

        // Title
        if ($this->_isSpecificVersion && !$this->_makeNew) {
            $fs->addField($this->_('Title'))->push(
                $this->html->textbox($this->fieldName('title'), $this->values->title)
                    ->setMaxLength(255)
                    ->isRequired(true)
            );
        }

        // Description
        $fs->addField($this->_('Description'))->push(
            $this->html->textbox($this->fieldName('description'), $this->values->description)
                ->setMaxLength(255)
        );

        if (!$this->_page->isNew() && !$this->_isSpecificVersion) {
            // New version
            $fs->addField()->push(
                $this->html->checkbox($this->fieldName('makeNew'), $this->values->makeNew, $this->_(
                    'Turn any changes into a new version'
                ))
            );
        }

        $i = 0;

        foreach ($this->_layout->getSlots() as $slot) {
            $form->addFieldSet($this->_('Slot %n%', ['%n%' => ++$i]))->addField()->push(
                Html::{'h3'}($slot->getName()),
                $this['slot-' . $slot->getId()]
            );
        }
    }

    protected function _renderLayoutSelector(aura\html\widget\IContainerWidget $form)
    {
        $fs = $form->addFieldSet($this->_('Page details'));

        // Layout
        $fs->addField($this->_('Layout'))->push(
            $this->html->select($this->fieldName('layout'), $this->values->layout, $this->_config->getLayoutList())
                ->isRequired(true)
                ->setNoSelectionLabel($this->_('Select layout...')),
            $this->html->eventButton($this->eventName('selectLayout'), $this->_('Select'))
                ->setIcon('accept')
                ->shouldValidate(false)
        );
    }


    // Events
    protected function onSelectLayoutEvent()
    {
        $validator = $this->data->newValidator()

            // Layout
            ->addRequiredField('layout', 'Enum')
                ->setOptions(array_keys($this->_config->getLayoutList()))

            ->validate($this->values);

        if ($this->isValid()) {
            $this->setStore('layout', $validator['layout']);
        }
    }

    protected function onResetLayoutEvent()
    {
        $this->setStore('layout', false);
    }



    // IO
    public function validate()
    {
        if (!$this->_layout) {
            $this->values->layout->addError('required', $this->_(
                'Please select a layout'
            ));

            return;
        }

        $validator = $this->data->newValidator()

            // Title
            ->chainIf($this->_isSpecificVersion && !$this->_makeNew, function ($validator) {
                $validator->addRequiredField('title', 'text')
                    ->setMaxLength(255);
            })

            // Description
            ->addField('description', 'text')
                ->setMaxLength(255)

            ->validate($this->values)
            ->applyTo($this->_page);

        $slots = [];

        foreach ($this->_layout->getSlots() as $slot) {
            $slots[$slot->getId()] = $this['slot-' . $slot->getId()]->as(ContentSlot::class)->apply();
        }

        if ($this->isValid()) {
            $layoutContent = new fire\layout\Content($this->_layout->getId());
            $layoutContent->addSlots($slots);
            $this->_page->body = $layoutContent->toXmlString(true);

            if (!($this->_isSpecificVersion && !$this->_makeNew)) {
                $this->_page->title = $this->_node['title'];
            }

            if ($this->_page->isNew()) {
                $this->_page->owner = Disciple::getId();
                $this->_page->node = $this->_node;
            } else {
                if (($this->_makeNew || $this->values['makeNew']) && $this->_page->hasChanged()) {
                    $this->_page->makeNew([
                        'date' => null
                    ]);
                } elseif ($this->_page['id'] == $this->_node['typeId']) {
                    $this->_node->title = $this->_page['title'];
                }
            }

            return $this->_page->getHistory();
        }
    }

    public function apply(): string
    {
        $this->_page->save();
        return (string)$this->_page['id'];
    }
}
