<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\arch\node;

use df;
use df\core;
use df\arch;
use df\fire;

abstract class Nightfire extends Base {

    public function dispatch() {
        $record = $this->data->nightfire->node->load($this->context->request);

        if($record !== null) {
            $this
                ->shouldCheckAccess(true)
                ->setDefaultAccess($record->getDefaultAccessValue())
                ->setCallback(function($node) use($record) {
                    return $record->createResponse($this->context);
                });
        }

        return parent::dispatch();
    }
}