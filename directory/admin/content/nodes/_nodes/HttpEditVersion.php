<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\content\nodes\_nodes;

use df\arch;
use df\fire;
use df\arch\node\form\NightfireTypeDelegate;

use DecodeLabs\Exceptional;

class HttpEditVersion extends arch\node\Form
{
    protected $_node;
    protected $_type;
    protected $_versionId;

    protected function init(): void
    {
        $this->_node = $this->scaffold->getRecord();
        $this->_type = $this->_node->getType();

        if (!$this->_type instanceof fire\type\IVersionedType) {
            throw Exceptional::{'df/fire/type/Implementation,Forbidden'}([
                'message' => 'Type is not versioned',
                'http' => 403
            ]);
        }

        $this->_versionId = $this->request['version'];

        if (!$this->_versionId) {
            throw Exceptional::{'df/fire/type/Version,NotFound'}([
                'message' => 'Version not found',
                'http' => 404
            ]);
        }
    }

    protected function getInstanceId(): ?string
    {
        return $this->_node['id'].':'.$this->_versionId;
    }

    protected function loadDelegates(): void
    {
        $this->_type->loadEditFormDelegate($this, 'type', $this->_node, $this->_versionId, false);
    }


    // Ui
    protected function createUi(): void
    {
        $form = $this->content->addForm();

        // Type
        $form->push($this['type']);

        // Buttons
        $form->addDefaultButtonGroup();
    }


    // Events
    protected function onSaveEvent()
    {
        $delegate = $this['type']->as(NightfireTypeDelegate::class);
        $delegate->validate();

        return $this->complete(function () use ($delegate) {
            $delegate->apply();
            $this->_node->save();
            $this->comms->flashSaveSuccess('content node version');
        });
    }
}
