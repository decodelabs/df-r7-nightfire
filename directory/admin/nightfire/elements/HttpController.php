<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\nightfire\elements;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpController extends arch\Controller {

    public function indexHtmlAction() {
        $view = $this->aura->getView('Index.html');

        $view['elementList'] = $this->data->nightfire->element->fetch()
            ->importRelationBlock('owner', 'link')
            ->paginateWith($this->request->query);

        return $view;
    }

    public function detailsHtmlAction() {
        $view = $this->aura->getView('Details.html');
        
        $view['element'] = $this->data->fetchForAction(
            'axis://nightfire/Element',
            $this->request->query['element']
        );

        return $view;
    }
}