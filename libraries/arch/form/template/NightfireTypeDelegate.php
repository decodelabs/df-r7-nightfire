<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\arch\form\template;

use df;
use df\core;
use df\arch;
use df\fire;
use df\aura;

abstract class NightfireTypeDelegate extends arch\form\Delegate implements fire\type\IFormDelegate {

    use arch\form\TForm_SelfContainedRenderableDelegate;

    protected $_node;
    protected $_versionId;
    protected $_makeNew = false;
    protected $_isSpecificVersion = false;

    public function setNode(fire\type\INode $node) {
        $this->_node = $node;
        return $this;
    }

    public function getNode() {
        return $this->_node;
    }

    public function setVersionId($versionId) {
        $this->_versionId = $versionId;
        return $this;
    }

    public function getVersionId() {
        return $this->_versionId;
    }

    public function shouldMakeNew($flag=null) {
        if($flag !== null) {
            $this->_makeNew = (bool)$flag;
            return $this;
        }

        return $this->_makeNew;
    }

    public function isSpecificVersion($flag=null) {
        if($flag !== null) {
            $this->_isSpecificVersion = (bool)$flag;
            return $this;
        }

        return $this->_isSpecificVersion;
    }

    public function getDefaultNodeValues() {
        return null;
    }

    public function renderContainerContent(aura\html\widget\IContainerWidget $container) {}
    public function validate() {}
}