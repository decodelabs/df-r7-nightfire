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
use df\fire;
    
class HttpController extends arch\Controller {

    public function indexHtmlAction() {
        $view = $this->aura->getView('Index.html');

        $view['nodeList'] = $this->data->nightfire->node->fetch()
            ->populateSelect('owner', 'id', 'fullName')
            ->paginateWith($this->request->query);

        return $view;
    }

    public function detailsHtmlAction() {
        $view = $this->aura->getView('Details.html');
        $this->_fetchNode($view);

        return $view;
    }

    public function versionsHtmlAction() {
        $view = $this->aura->getView('Versions.html');
        $this->_fetchNode($view);
        $type = $view['node']->getType();

        if(!$type instanceof fire\type\IVersionedType) {
            $this->throwError(403, 'Type not versioned');
        }

        $view['versionList'] = $type->getVersionList($view['node']);

        return $view;
    }

    protected function _fetchNode($view) {
        $view['node'] = $this->data->fetchForAction(
            'axis://nightfire/Node',
            $this->request->query['node']
        );
    }


    public function previewAction() {
        $node = $this->data->fetchForAction(
            'axis://nightfire/Node',
            $this->request->query['node']
        );

        $context = $this->context->spawnInstance($node['slug']);
        $type = $node->getType();
        return $type->createResponse($context, $node, $this->request->query['version']);
    }
}