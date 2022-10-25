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

use DecodeLabs\Exceptional;

class HttpActivateVersion extends arch\node\ConfirmForm
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

        if ($this->_node->getTypeId() == $this->_versionId) {
            throw Exceptional::{'df/fire/type/Version,Forbidden'}([
                'message' => 'Version is already active',
                'http' => 403
            ]);
        }
    }

    protected function getInstanceId(): ?string
    {
        return $this->_node['id'].':'.$this->_versionId;
    }

    protected function setDefaultValues(): void
    {
        $this->values->keepCurrent = true;
    }

    protected function getMainMessage()
    {
        return $this->_('Are you sure you want to activate this version?');
    }

    protected function createItemUi($container)
    {
        $container->addField()->push(
            $this->html->checkbox('deleteUnused', $this->values->deleteUnused, $this->_(
                'Delete unused versions'
            ))
        );

        $container->addField()->push(
            $this->html->checkbox('keepCurrent', $this->values->keepCurrent, $this->_(
                'Keep current active version'
            ))
        );
    }

    protected function customizeMainButton($button)
    {
        $button->setBody($this->_('Activate'));
    }

    protected function apply()
    {
        $validator = $this->data->newValidator()
            ->addRequiredField('deleteUnused', 'boolean')
            ->addRequiredField('keepCurrent', 'boolean')
            ->validate($this->values);

        $this->_type->applyVersion(
            $this->_node,
            $this->_versionId,
            $validator['deleteUnused'],
            $validator['keepCurrent']
        );

        $this->_node->versionCount = $this->_type->countVersions($this->_node);
        $this->_node->currentVersion = $this->_type->getVersionNumber($this->_node);
        $this->_node->save();
    }
}
