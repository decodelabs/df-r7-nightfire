<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\shared\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpRoot extends arch\Action {

    const DEFAULT_ACCESS = arch\IAccess::ALL;
    const CHECK_ACCESS = false;

    public function execute() {
        return $this->data->nightfire->node->load($this->context);
    }
}