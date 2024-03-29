<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\arch\node\form;

use df\arch;
use df\aura;
use df\fire;

abstract class NightfireTypeDelegate extends Delegate implements fire\type\IFormDelegate
{
    use arch\node\TForm_SelfContainedRenderableDelegate;

    protected $_node;
    protected $_versionId;
    protected $_makeNew = false;
    protected $_isSpecificVersion = false;

    public function setNode(fire\type\INode $node)
    {
        $this->_node = $node;
        return $this;
    }

    public function getNode()
    {
        return $this->_node;
    }

    public function setVersionId($versionId)
    {
        $this->_versionId = $versionId;
        return $this;
    }

    public function getVersionId()
    {
        return $this->_versionId;
    }

    public function shouldMakeNew(bool $flag = null)
    {
        if ($flag !== null) {
            $this->_makeNew = $flag;
            return $this;
        }

        return $this->_makeNew;
    }

    public function isSpecificVersion(bool $flag = null)
    {
        if ($flag !== null) {
            $this->_isSpecificVersion = $flag;
            return $this;
        }

        return $this->_isSpecificVersion;
    }

    public function getDefaultNodeValues()
    {
        return null;
    }

    public function renderContainerContent(aura\html\widget\IContainerWidget $container)
    {
    }
    public function validate()
    {
    }
}
