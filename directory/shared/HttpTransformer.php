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

            return (new arch\node\Base($context, function($node) {
                return $node->context->http->redirect($node->context->request)
                    ->isPermanent(true);
            }));
        }
        */

        $record = $this->data->nightfire->node->load($this->context->request);

        if($record === null) {
            return null;
        }

        return (new arch\node\Base($this->context, function($node) use($record) {
                return $record->createResponse($node->context);
            }))
            ->shouldCheckAccess(true)
            ->setDefaultAccess($record->getDefaultAccessValue());
    }

    public function canDeliver() {
        return $this->data->nightfire->node->exists($this->context->request);
    }
}