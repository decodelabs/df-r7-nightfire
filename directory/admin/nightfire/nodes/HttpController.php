<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\nightfire\nodes;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpController extends arch\Controller {

    public function indexHtmlAction() {
        $view = $this->aura->getView('Index.html');

        $view['nodeList'] = $this->data->nightfire->node->fetch()
            ->populate('owner')
            ->paginateWith($this->request->query);

        return $view;
    }

    public function detailsHtmlAction() {
        $view = $this->aura->getView('Details.html');
        $this->_fetchNode($view);

        return $view;
    }

    protected function _fetchNode($view) {
        $view['node'] = $this->data->fetchForAction(
            'axis://nightfire/Node',
            $this->request->query['node']
        );
    }
}