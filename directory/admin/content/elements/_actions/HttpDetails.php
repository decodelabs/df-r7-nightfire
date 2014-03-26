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

class HttpDetails extends arch\Action {
    
    public function executeAsHtml() {
        $view = $this->aura->getView('Details.html');
        
        $view['element'] = $this->data->fetchForAction(
            'axis://nightfire/Element',
            $this->request->query['element']
        );

        return $view;
    }
}