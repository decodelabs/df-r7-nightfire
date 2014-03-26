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

class HttpIndex extends arch\Action {
    
    public function executeAsHtml() {
        $view = $this->aura->getView('Index.html');

        $view['nodeList'] = $this->data->nightfire->node->fetch()
            ->importRelationBlock('owner', 'link')
            ->paginateWith($this->request->query);

        return $view;
    }
}