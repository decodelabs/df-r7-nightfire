<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\nodes\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\fire;
    
class HttpActivateVersion extends arch\form\template\Confirm {

    protected $_node;
    protected $_type;
    protected $_versionId;

    protected function _init() {
        $this->_node = $this->scaffold->getRecord();
        $this->_type = $this->_node->getType();

        if(!$this->_type instanceof fire\type\IVersionedType) {
            $this->throwError(403, 'Type is not versioned');
        }

        $this->_versionId = $this->request->query['version'];

        if($this->_node->getTypeId() == $this->_versionId) {
            $this->throwError(403, 'Version is already active');
        }
    }

    protected function _getDataId() {
        return $this->_node['id'].':'.$this->_versionId;
    }

    protected function _setDefaultValues() {
        $this->values->keepCurrent = true;
    }

    protected function _getMainMessage($itemName) {
        return $this->_('Are you sure you want to activate this version?');
    }

    protected function _renderItemDetails($container) {
        $container->addFieldArea()->push(
            $this->html->checkbox('deleteUnused', $this->values->deleteUnused, $this->_(
                'Delete unused versions'
            ))
        );

        $container->addFieldArea()->push(
            $this->html->checkbox('keepCurrent', $this->values->keepCurrent, $this->_(
                'Keep current active version'
            ))
        );
    }

    protected function _getMainButtonText() {
        return $this->_('Activate');
    }

    protected function _apply() {
        $validator = $this->data->newValidator()
            ->addField('deleteUnused', 'boolean')
            ->addField('keepCurrent', 'boolean')
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