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

        /*
        // See if the url just needs a /
        $url = $this->context->http->getRequest()->getUrl();

        if($url->path->shouldAddTrailingSlash()) {
            $testUrl = clone $url;
            $testUrl->path->shouldAddTrailingSlash(false);
            $context = clone $this->context;
            $context->location = $context->request = $this->context->http->getRouter()->urlToRequest($testUrl);

            return (new arch\action\Base($context, function($action) {
                return $action->context->http->redirect($action->context->request)
                    ->isPermanent(true);
            }));
        }
        */

        $node = $this->data->nightfire->node->load($this->context->request);

        if($node === null) {
            return null;
        }

        return (new arch\action\Base($this->context, function($action) use($node) {
                return $node->createResponse($action->context);
            }))
            ->shouldCheckAccess(true)
            ->setDefaultAccess($node->getDefaultAccessValue());
    }

    public function canDeliver() {
        return $this->data->nightfire->node->exists($this->context->request);
    }
}