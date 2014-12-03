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
        try {
            return $this->data->nightfire->node->load($this->context);
        } catch(\Exception $e) {
            if($e->getCode() == 404) {
                return parent::dispatch();
            }

            throw $e;
        }
    }

    protected function _dispatchRootAction() {
        return null;
    }
}