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

class HttpDeleteVersion extends arch\node\DeleteForm {

    const ITEM_NAME = 'version';

    protected $_node;
    protected $_type;
    protected $_version;

    protected function init() {
        $this->_node = $this->scaffold->getRecord();
        $this->_type = $this->_node->getType();

        if(!$this->_type instanceof fire\type\IVersionedType) {
            throw core\Error::{'fire/type/EImplementation'}([
                'message' => 'Type is not versioned',
                'http' => 403
            ]);
        }

        $this->_version = $this->_type->getVersion($this->_node, $this->request['version']);

        if(!$this->_version) {
            throw core\Error::{'fire/type/EVersion,ENotFound'}([
                'message' => 'Version not found',
                'http' => 404
            ]);
        }

        if($this->_version->isActive($this->_node)) {
            throw core\Error::{'fire/type/EVersion,ELogic'}([
                'message' => 'Version is active',
                'http' => 403
            ]);
        }
    }

    protected function getInstanceId() {
        return $this->_node['id'].':'.$this->_version['id'];
    }

    protected function createItemUi($container) {
        $container->addAttributeList($this->_version)
            ->addField('title')
            ->addField('type', function($version) {
                return $this->_node['type'];
            })
            ->addField('owner', function($version) {
                return $this->apex->component('~admin/users/clients/UserLink', $version['owner']);
            })
            ->addField('date', $this->_('Created'), function($version) {
                return $this->html->timeSince($version['date']);
            })
            ->addField('preview', function($version) {
                return $this->_type->renderPreview($this->view, $this->_node, $version);
            })
            ;
    }

    protected function apply() {
        $this->_type->deleteVersion($this->_node, $this->_version);

        if($this->_type instanceof fire\type\IVersionedType) {
            $this->_node->versionCount = $this->_type->countVersions($this->_node);
            $this->_node->currentVersion = $this->_type->getVersionNumber($this->_node);
            $this->_node->save();
        }
    }
}