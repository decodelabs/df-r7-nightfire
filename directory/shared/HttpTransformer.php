<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\shared;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpTransformer extends arch\Transformer {
    
    public function execute() {
        $node = $this->data->nightfire->node->load($this->context->request);

        if($node === null) {
            return null;
        }

        return (new arch\Action($this->context, function($action) use($node) {
                return $node->createResponse($action->context);
            }))
            ->shouldCheckAccess(true)
            ->setDefaultAccess($node->getDefaultAccessValue());
    }

    public function canDeliver() {
        return $this->data->nightfire->node->exists($this->context->request);
    }
}