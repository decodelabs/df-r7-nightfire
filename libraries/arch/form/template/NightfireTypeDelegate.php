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
    
abstract class NightfireTypeDelegate extends arch\form\Delegate implements fire\type\IFormDelegate {

    use arch\form\TForm_SelfContainedRenderableDelegate;

    protected $_node;

    public function setNode(fire\type\INode $node) {
        $this->_node = $node;
        return $this;
    }

    public function getNode() {
        return $this->_node;
    }
}