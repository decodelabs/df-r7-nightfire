<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\nodes\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpPreview extends arch\Action {
    
    public function execute() {
        $node = $this->scaffold->getRecord();
        $context = $this->context->spawnInstance($node['slug']);
        $type = $node->getType();
        
        return $type->createResponse($context, $node, $this->request->query['version']);
    }
}