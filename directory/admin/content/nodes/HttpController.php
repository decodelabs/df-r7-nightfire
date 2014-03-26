<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\fire;
    
class HttpController extends arch\Controller {

    public function fetchNode($view) {
        $view['node'] = $this->data->fetchForAction(
            'axis://nightfire/Node',
            $this->request->query['node']
        );
    }
}