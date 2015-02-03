<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\arch;

use df;
use df\core;
use df\arch;
use df\fire;
    
abstract class NightfireAction extends arch\Action {

    public function dispatch() {
        $response = $this->data->nightfire->node->load($this->context);

        if($response === null) {
            return parent::dispatch();
        }

        return $response;
    }
}