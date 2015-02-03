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
        $node = $this->data->nightfire->node->load($this->context->request);

        if($node !== null) {
            $this
                ->shouldCheckAccess(true)
                ->setDefaultAccess($node->getDefaultAccessValue())
                ->setCallback(function($action) use($node) {
                    return $node->createResponse($this->context);
                });
        }

        return parent::dispatch();
    }
}