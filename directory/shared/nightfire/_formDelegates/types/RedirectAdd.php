<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\shared\nightfire\_formDelegates\types;

use df\arch;
use df\aura;

class RedirectAdd extends arch\node\form\NightfireTypeDelegate
{
    protected function setDefaultValues(): void
    {
        $this->values->url = $this->_node['typeData'];
    }

    public function renderContainerContent(aura\html\widget\IContainerWidget $form)
    {
        $fs = $form->addFieldSet($this->_('Redirect details'));

        // Url
        $fs->addField($this->_('Url'))->push(
            $this->html->textbox($this->fieldName('url'), $this->values->url)
                ->isRequired(true)
        );
    }

    public function validate()
    {
        $this->data->newValidator()
            ->addRequiredField('url', 'text')
                ->setRecordName('typeData')
            ->validate($this->values)
            ->applyTo($this->_node);

        if ($this->_node->hasChanged('typeData')) {
            return ['Updated url to ' . $this->values['url']];
        }
    }

    public function apply(): mixed
    {
        return null;
    }
}
