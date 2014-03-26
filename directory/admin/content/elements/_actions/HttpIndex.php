<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\elements\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpIndex extends arch\Action {
    
    public function executeAsHtml() {
        $view = $this->aura->getView('Index.html');

        $view['elementList'] = $this->data->nightfire->element->fetch()
            ->importRelationBlock('owner', 'link')
            ->paginateWith($this->request->query);

        return $view;
    }
}