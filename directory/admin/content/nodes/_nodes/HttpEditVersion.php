<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\nodes\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\fire;
use df\opal;

use DecodeLabs\Glitch;

class HttpEditVersion extends arch\node\Form
{
    protected $_node;
    protected $_type;
    protected $_versionId;

    protected function init()
    {
        $this->_node = $this->scaffold->getRecord();
        $this->_type = $this->_node->getType();

        if (!$this->_type instanceof fire\type\IVersionedType) {
            throw Glitch::{'df/fire/type/EImplementation,EForbidden'}([
                'message' => 'Type is not versioned',
                'http' => 403
            ]);
        }

        $this->_versionId = $this->request['version'];

        if (!$this->_versionId) {
            throw Glitch::{'df/fire/type/EVersion,ENotFound'}([
                'message' => 'Version not found',
                'http' => 404
            ]);
        }
    }

    protected function getInstanceId()
    {
        return $this->_node['id'].':'.$this->_versionId;
    }

    protected function loadDelegates()
    {
        $this->_type->loadEditFormDelegate($this, 'type', $this->_node, $this->_versionId, false);
    }


    // Ui
    protected function createUi()
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
        $delegate = $this['type'];
        $delegate->validate();

        return $this->complete(function () use ($delegate) {
            $delegate->apply();
            $this->_node->save();
            $this->comms->flashSaveSuccess('content node version');
        });
    }
}
