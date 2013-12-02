<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\shared\nightfire\_formDelegates\types;

use df;
use df\core;
use df\apex;
use df\arch;
use df\fire;
use df\aura;
    
class PageDelete extends arch\form\template\NightfireTypeDelegate {

    public function apply() {
        $this->data->nightfire->page->delete()
            ->where('node', '=', $this->_node)
            ->execute();
    }
}